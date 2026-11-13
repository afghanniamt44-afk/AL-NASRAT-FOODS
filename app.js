"use strict";

const CURRENCY_KEY = "al-nasrat-foods-currency-v1";
const EXCHANGE_RATES = Object.freeze({ AFN: 1, PKR: 0.99, USD: 0.0142 });
const CURRENCY_META = Object.freeze({
    AFN: { locale: "fa-AF-u-nu-latn", currency: "AFN" },
    PKR: { locale: "ur-PK-u-nu-latn", currency: "PKR" },
    USD: { locale: "en-US", currency: "USD" },
});

let items = [];
let customers = [];
let activeCurrency = "AFN";
let toastTimeout;
let lastFocusedElement;

const inventoryBody = document.querySelector("#inventory-body");
const inventorySearch = document.querySelector("#inventory-search");
const modalBackdrop = document.querySelector("#modal-backdrop");
const stockForm = document.querySelector("#stock-form");
const stockItem = document.querySelector("#stock-item");
const stockQuantity = document.querySelector("#stock-quantity");
const stockNote = document.querySelector("#stock-note");
const formError = document.querySelector("#form-error");
const currencySelect = document.querySelector("#currency-select");
const toast = document.querySelector("#toast");
const customerModal = document.querySelector("#customer-modal-backdrop");
const customerForm = document.querySelector("#customer-form");
const customerFormError = document.querySelector("#customer-form-error");

// --- API Helpers ---
async function fetchAPI(action, data = null) {
    try {
        const options = data
            ? {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            }
            : { method: "GET" };
        const res = await fetch(`api.php?action=${encodeURIComponent(action)}`, options);
        return await res.json();
    } catch (err) {
        console.error("API error:", err);
        return { success: false, error: err.message };
    }
}

async function loadDataFromDatabase() {
    // Currency
    const currRes = await fetchAPI("get_currency");
    if (currRes && currRes.currency) {
        activeCurrency = currRes.currency;
        if (currencySelect) currencySelect.value = activeCurrency;
    }

    // Inventory
    const invRes = await fetchAPI("get_inventory");
    if (invRes && invRes.success) {
        items = invRes.data || [];
        renderInventory();
    }

    // Customers
    const custRes = await fetchAPI("get_customers");
    if (custRes && custRes.success) {
        customers = custRes.data || [];
        renderCustomers();
        renderMoney();
    }
}

function formatNumber(value, maximumFractionDigits = 0) {
    return new Intl.NumberFormat("en-US", {
        maximumFractionDigits,
        minimumFractionDigits: maximumFractionDigits,
    }).format(value);
}

function formatMoney(afnValue) {
    const meta = CURRENCY_META[activeCurrency] || CURRENCY_META.AFN;
    const value = afnValue * (EXCHANGE_RATES[activeCurrency] || 1);
    return new Intl.NumberFormat(meta.locale, {
        style: "currency",
        currency: meta.currency,
        maximumFractionDigits: activeCurrency === "USD" ? 2 : 0,
    }).format(value);
}

function renderMoney() {
    document.querySelectorAll("[data-money]").forEach((element) => {
        const amount = Number(element.dataset.money);
        if (Number.isFinite(amount)) element.textContent = formatMoney(amount);
    });
    const footerCurr = document.querySelector("#footer-currency");
    if (footerCurr) footerCurr.textContent = activeCurrency;
}

function renderCustomers() {
    const tableBody = document.querySelector("#customer-table-body");
    if (!tableBody) return;
    tableBody.replaceChildren(...customers.map((customer, index) => {
        const row = document.createElement("tr");
        const statusClass = customer.status === "Overdue"
            ? "overdue"
            : customer.status === "Due soon" ? "due" : "current";
        row.innerHTML = `
      <td class="pl-5 sm:pl-6"><div class="flex items-center gap-2.5"><span class="customer-avatar avatar-${(index % 4) + 1}">${escapeHtml(customer.initials)}</span><span><span class="block text-[10px] font-medium text-stone-200">${escapeHtml(customer.name)}</span><span class="mt-1 block text-[9px] text-stone-600">${escapeHtml(customer.business)} · ${escapeHtml(customer.location)}</span></span></div></td>
      <td class="text-[10px] text-stone-400">${escapeHtml(customer.lastPayment || customer.last_payment || '')}</td>
      <td class="font-mono text-[10px] text-stone-200">${formatMoney(customer.balance)}</td>
      <td><span class="status-badge ${statusClass}">${escapeHtml(customer.status)}</span></td>
      <td class="pr-5 text-right sm:pr-6"><button class="remind-button" type="button" data-customer="${escapeHtml(customer.name)}">Remind</button></td>`;
        return row;
    }));

    const total = customers.reduce((sum, customer) => sum + (Number(customer.balance) || 0), 0);
    const activeCustomers = customers.filter((customer) => (Number(customer.balance) || 0) > 0);
    const bakayaSummary = document.querySelector("#bakaya-summary-total");
    if (bakayaSummary) bakayaSummary.textContent = formatMoney(total);
    const bakayaTotal = document.querySelector("#bakaya-total");
    if (bakayaTotal) bakayaTotal.textContent = formatMoney(total);

    const activeCountElem = document.querySelector("#active-customer-count");
    if (activeCountElem) activeCountElem.textContent = `${activeCustomers.length} active customers`;

    const noteElem = document.querySelector("#customer-count-note");
    if (noteElem) {
        noteElem.textContent = `Showing ${customers.length} ${customers.length === 1 ? "customer" : "customers"} with khata accounts`;
    }

    const avatarContainer = document.querySelector("#customer-avatar-list");
    if (avatarContainer) {
        avatarContainer.replaceChildren(...activeCustomers.slice(0, 3).map((customer, index) => {
            const avatar = document.createElement("span");
            avatar.className = `customer-dot avatar-${(index % 4) + 1}`;
            avatar.textContent = customer.initials;
            avatar.title = customer.name;
            return avatar;
        }));
        if (activeCustomers.length > 3) {
            const remaining = document.createElement("span");
            remaining.className = "customer-dot";
            remaining.textContent = `+${activeCustomers.length - 3}`;
            avatarContainer.append(remaining);
        }
    }
}

function itemStatus(item) {
    return Number(item.quantity) <= Number(item.minimum)
        ? { label: "Low stock", isLow: true }
        : { label: "In stock", isLow: false };
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;",
    })[character]);
}

function renderInventory() {
    if (!inventoryBody) return;
    const query = (inventorySearch?.value || "").trim().toLocaleLowerCase();
    const filtered = items.filter((item) =>
        `${item.name} ${item.category}`.toLocaleLowerCase().includes(query),
    );
    inventoryBody.replaceChildren(...filtered.map((item) => {
        const status = itemStatus(item);
        const row = document.createElement("tr");
        row.innerHTML = `
      <td class="pl-5 sm:pl-6"><div class="flex items-center gap-2.5"><span class="item-icon" aria-hidden="true">${escapeHtml(item.icon)}</span><span><span class="item-name block">${escapeHtml(item.name)}</span><span class="item-category block">${escapeHtml(item.category)}</span></span></div></td>
      <td><span class="stock-number${status.isLow ? " low" : ""}">${formatNumber(item.quantity, item.quantity % 1 ? 2 : 0)}</span><span class="stock-unit">${escapeHtml(item.unit)}</span></td>
      <td><span class="status-badge${status.isLow ? " low-stock" : ""}">${status.label}</span></td>
      <td><span class="movement-note block">${escapeHtml(item.movement)}</span><span class="movement-time block">${escapeHtml(item.time)}</span></td>
      <td class="pr-5 text-right sm:pr-6"><div class="inline-flex gap-1.5">
        <button class="quick-button" type="button" data-action="in" data-item="${item.id}" aria-label="Add ${escapeHtml(item.name)} stock"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
        <button class="quick-button" type="button" data-action="out" data-item="${item.id}" aria-label="Remove ${escapeHtml(item.name)} stock"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button>
        <button class="quick-button" type="button" data-crud="edit" data-item="${item.id}" aria-label="Edit ${escapeHtml(item.name)}" title="Edit material"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.2-.8L19 8.4a2.1 2.1 0 0 0-3-3L5.2 16.2 4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        <button class="quick-button delete-button" type="button" data-crud="delete" data-item="${item.id}" aria-label="Delete ${escapeHtml(item.name)}" title="Delete material"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16m-10 4v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
      </div></td>`;
        return row;
    }));

    const lowStockCount = items.filter((item) => itemStatus(item).isLow).length;
    const totalStock = items.reduce((total, item) => total + (Number(item.quantity) || 0), 0);
    const countElem = document.querySelector("#inventory-count");
    if (countElem) countElem.textContent = `Showing ${filtered.length} of ${items.length} raw materials`;
    const stockTotalElem = document.querySelector("#stock-total");
    if (stockTotalElem) stockTotalElem.textContent = formatNumber(totalStock);
    const lowStockElem = document.querySelector("#low-stock-count");
    if (lowStockElem) lowStockElem.textContent = `${lowStockCount} ${lowStockCount === 1 ? "item" : "items"}`;
    const navStockCount = document.querySelector("#nav-stock-count");
    if (navStockCount) navStockCount.textContent = String(items.length).padStart(2, "0");
}

function populateItemOptions(selectedId) {
    if (!stockItem) return;
    stockItem.replaceChildren(...items.map((item) => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = `${item.name} · ${formatNumber(item.quantity)} ${item.unit} on hand`;
        if (item.id === selectedId) option.selected = true;
        return option;
    }));
}

function setMovement(type) {
    document.querySelectorAll(".movement-option").forEach((option) => {
        const active = option.dataset.movement === type;
        option.classList.toggle("is-active", active);
        option.setAttribute("aria-pressed", String(active));
    });
    const radio = stockForm?.querySelector(`input[name="movement"][value="${type}"]`);
    if (radio) radio.checked = true;
}

function openStockModal({ itemId, movement = "in" } = {}) {
    if (!items.length) {
        showToast("Add raw materials first.", true);
        return;
    }
    lastFocusedElement = document.activeElement;
    populateItemOptions(itemId ?? items[0]?.id);
    setMovement(movement);
    if (stockQuantity) stockQuantity.value = "";
    if (stockNote) stockNote.value = "";
    if (formError) {
        formError.textContent = "";
        formError.classList.add("hidden");
    }
    if (modalBackdrop) {
        modalBackdrop.classList.remove("hidden");
        modalBackdrop.setAttribute("aria-hidden", "false");
    }
    document.body.classList.add("overflow-hidden");
    if (stockQuantity) stockQuantity.focus();
}

function closeStockModal() {
    if (modalBackdrop) {
        modalBackdrop.classList.add("hidden");
        modalBackdrop.setAttribute("aria-hidden", "true");
    }
    document.body.classList.remove("overflow-hidden");
    if (lastFocusedElement instanceof HTMLElement) lastFocusedElement.focus();
}

function openMaterialModal(itemToEdit = null) {
    lastFocusedElement = document.activeElement;
    const form = document.querySelector("#material-form");
    const title = document.querySelector("#material-modal-title");
    const error = document.querySelector("#material-form-error");
    const backdrop = document.querySelector("#material-modal-backdrop");
    if (!form || !backdrop) return;
    form.reset();
    error.textContent = "";
    error.classList.add("hidden");
    if (itemToEdit) {
        title.textContent = "Edit raw material";
        document.querySelector("#material-id").value = itemToEdit.id;
        document.querySelector("#material-name").value = itemToEdit.name;
        document.querySelector("#material-category").value = itemToEdit.category;
        document.querySelector("#material-quantity").value = itemToEdit.quantity;
        document.querySelector("#material-minimum").value = itemToEdit.minimum;
        document.querySelector("#material-unit").value = itemToEdit.unit;
    } else {
        title.textContent = "Add raw material";
        document.querySelector("#material-id").value = "";
        document.querySelector("#material-unit").value = "kg";
    }
    backdrop.classList.remove("hidden");
    backdrop.setAttribute("aria-hidden", "false");
    document.body.classList.add("overflow-hidden");
    document.querySelector("#material-name").focus();
}

function closeMaterialModal() {
    const backdrop = document.querySelector("#material-modal-backdrop");
    if (!backdrop) return;
    backdrop.classList.add("hidden");
    backdrop.setAttribute("aria-hidden", "true");
    document.body.classList.remove("overflow-hidden");
    if (lastFocusedElement instanceof HTMLElement) lastFocusedElement.focus();
}

function showToast(message, isError = false) {
    if (!toast) return;
    window.clearTimeout(toastTimeout);
    toast.classList.toggle("toast-error", isError);
    toast.innerHTML = `<span class="toast-icon" aria-hidden="true">${isError ? "!" : "✓"}</span><span>${escapeHtml(message)}</span>`;
    toast.classList.remove("hidden");
    toastTimeout = window.setTimeout(() => toast.classList.add("hidden"), 3200);
}

function updateClock() {
    const now = new Date();
    const timeElem = document.querySelector("#local-time");
    if (timeElem) {
        timeElem.textContent = new Intl.DateTimeFormat("en-GB", {
            timeZone: "Asia/Kabul", hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false,
        }).format(now);
    }
    const dateElem = document.querySelector("#greeting-date");
    if (dateElem) {
        dateElem.textContent = new Intl.DateTimeFormat("en-US", {
            timeZone: "Asia/Kabul", weekday: "long", month: "long", day: "numeric",
        }).format(now);
    }
}

function openCustomerModal() {
    if (!customerModal || !customerForm) return;
    lastFocusedElement = document.activeElement;
    customerForm.reset();
    if (customerFormError) {
        customerFormError.textContent = "";
        customerFormError.classList.add("hidden");
    }
    customerModal.classList.remove("hidden");
    customerModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("overflow-hidden");
    document.querySelector("#customer-name")?.focus();
}

function closeCustomerModal() {
    if (!customerModal) return;
    customerModal.classList.add("hidden");
    customerModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("overflow-hidden");
    if (lastFocusedElement instanceof HTMLElement) lastFocusedElement.focus();
}

// Event Listeners
document.querySelector("#stock-in-top")?.addEventListener("click", () => openStockModal());
document.querySelector("#stock-in-button")?.addEventListener("click", () => openStockModal());
document.querySelector("#modal-close")?.addEventListener("click", closeStockModal);
document.querySelector("#modal-cancel")?.addEventListener("click", closeStockModal);

modalBackdrop?.addEventListener("click", (event) => {
    if (event.target === modalBackdrop) closeStockModal();
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        if (modalBackdrop && !modalBackdrop.classList.contains("hidden")) closeStockModal();
        if (customerModal && !customerModal.classList.contains("hidden")) closeCustomerModal();
        const matModal = document.querySelector("#material-modal-backdrop");
        if (matModal && !matModal.classList.contains("hidden")) closeMaterialModal();
    }
});

document.querySelectorAll(".movement-option").forEach((option) => {
    option.addEventListener("click", () => setMovement(option.dataset.movement));
});

inventoryBody?.addEventListener("click", async (event) => {
    const button = event.target.closest("button[data-item]");
    if (!button) return;
    if (button.dataset.crud === "edit") {
        const item = items.find((entry) => entry.id === button.dataset.item);
        if (item) openMaterialModal(item);
        return;
    }
    if (button.dataset.crud === "delete") {
        const item = items.find((entry) => entry.id === button.dataset.item);
        if (!item) return;
        if (!window.confirm(`Delete ${item.name} from inventory? This cannot be undone.`)) return;

        const res = await fetchAPI("delete_inventory_item", { id: item.id });
        if (res.success) {
            items = items.filter((entry) => entry.id !== item.id);
            renderInventory();
            showToast(`${item.name} deleted from database.`);
        } else {
            showToast("Failed to delete item: " + (res.error || "Unknown error"), true);
        }
        return;
    }
    openStockModal({ itemId: button.dataset.item, movement: button.dataset.action });
});

inventorySearch?.addEventListener("input", renderInventory);

document.querySelector("#add-material-button")?.addEventListener("click", () => openMaterialModal());
document.querySelector("#material-modal-close")?.addEventListener("click", closeMaterialModal);
document.querySelector("#material-modal-cancel")?.addEventListener("click", closeMaterialModal);

const matBackdrop = document.querySelector("#material-modal-backdrop");
matBackdrop?.addEventListener("click", (event) => {
    if (event.target === event.currentTarget) closeMaterialModal();
});

document.querySelector("#material-form")?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const id = document.querySelector("#material-id").value;
    const name = document.querySelector("#material-name").value.trim();
    const category = document.querySelector("#material-category").value.trim();
    const quantity = Number(document.querySelector("#material-quantity").value);
    const minimum = Number(document.querySelector("#material-minimum").value);
    const unit = document.querySelector("#material-unit").value.trim();
    const error = document.querySelector("#material-form-error");

    const duplicateName = items.some((item) =>
        item.id !== id && item.name.toLocaleLowerCase() === name.toLocaleLowerCase(),
    );
    if (!name || !category || !unit || !Number.isFinite(quantity) || quantity < 0
        || !Number.isFinite(minimum) || minimum < 0) {
        error.textContent = "Enter a name, category, unit, and valid non-negative stock quantities.";
        error.classList.remove("hidden");
        return;
    }
    if (duplicateName) {
        error.textContent = "A raw material with this name already exists.";
        error.classList.remove("hidden");
        return;
    }

    const payload = { id, name, category, quantity, minimum, unit };
    const res = await fetchAPI("save_inventory_item", payload);
    if (res.success) {
        await loadDataFromDatabase();
        closeMaterialModal();
        showToast(id ? `${name} updated in database.` : `${name} saved to database.`);
    } else {
        error.textContent = res.error || "Failed to save item.";
        error.classList.remove("hidden");
    }
});

stockForm?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const item = items.find((candidate) => candidate.id === stockItem.value);
    const quantity = Number(stockQuantity.value);
    const movement = stockForm.elements.movement.value;
    const note = stockNote.value.trim();

    if (!item || !Number.isFinite(quantity) || quantity <= 0) {
        formError.textContent = "Choose an item and enter a valid quantity greater than zero.";
        formError.classList.remove("hidden");
        return;
    }
    if (movement === "out" && quantity > item.quantity) {
        formError.textContent = `Only ${formatNumber(item.quantity)} ${item.unit} are available.`;
        formError.classList.remove("hidden");
        stockQuantity.focus();
        return;
    }

    const res = await fetchAPI("adjust_stock", {
        id: item.id,
        amount: quantity,
        type: movement,
        note: note
    });

    if (res.success) {
        await loadDataFromDatabase();
        closeStockModal();
        showToast(`${item.name}: ${movement === "in" ? "+" : "−"}${formatNumber(quantity, quantity % 1 ? 2 : 0)} ${item.unit} updated in database.`);
    } else {
        formError.textContent = res.error || "Failed to adjust stock.";
        formError.classList.remove("hidden");
    }
});

currencySelect?.addEventListener("change", async () => {
    if (!Object.hasOwn(CURRENCY_META, currencySelect.value)) return;
    const res = await fetchAPI("set_currency", { currency: currencySelect.value });
    if (res.success) {
        activeCurrency = currencySelect.value;
        renderMoney();
        renderCustomers();
    }
});

document.querySelectorAll(".add-customer-trigger").forEach((button) => {
    button.addEventListener("click", openCustomerModal);
});
document.querySelector("#customer-modal-close")?.addEventListener("click", closeCustomerModal);
document.querySelector("#customer-modal-cancel")?.addEventListener("click", closeCustomerModal);

customerModal?.addEventListener("click", (event) => {
    if (event.target === customerModal) closeCustomerModal();
});

customerForm?.addEventListener("submit", async (event) => {
    event.preventDefault();
    const name = document.querySelector("#customer-name").value.trim();
    const business = document.querySelector("#customer-business").value.trim();
    const location = document.querySelector("#customer-location").value.trim();
    const openingBalance = Number(document.querySelector("#customer-balance").value || 0);

    if (!name || !business || !location || !Number.isFinite(openingBalance) || openingBalance < 0) {
        customerFormError.textContent = "Enter a customer name, business type, location, and a valid opening balance.";
        customerFormError.classList.remove("hidden");
        return;
    }

    const res = await fetchAPI("add_customer", {
        name,
        business,
        location,
        balance: openingBalance
    });

    if (res.success) {
        await loadDataFromDatabase();
        closeCustomerModal();
        showToast(`${name} added to khata database.`);
    } else {
        customerFormError.textContent = res.error || "Failed to add customer.";
        customerFormError.classList.remove("hidden");
    }
});

document.querySelector("#customer-table-body")?.addEventListener("click", (event) => {
    const button = event.target.closest(".remind-button");
    if (button) showToast(`Reminder noted for ${button.dataset.customer}.`);
});

const menuToggle = document.querySelector("#menu-toggle");
const sidebar = document.querySelector("#sidebar");
let sidebarShade;
menuToggle?.addEventListener("click", () => {
    const isOpen = sidebar.classList.toggle("is-open");
    menuToggle.setAttribute("aria-expanded", String(isOpen));
    if (isOpen) {
        sidebarShade = document.createElement("button");
        sidebarShade.type = "button";
        sidebarShade.className = "sidebar-shade";
        sidebarShade.setAttribute("aria-label", "Close navigation");
        sidebarShade.addEventListener("click", closeSidebar);
        document.body.append(sidebarShade);
    } else {
        closeSidebar();
    }
});

function closeSidebar() {
    sidebar?.classList.remove("is-open");
    menuToggle?.setAttribute("aria-expanded", "false");
    sidebarShade?.remove();
    sidebarShade = null;
}
sidebar?.querySelectorAll(".nav-link").forEach((link) => link.addEventListener("click", closeSidebar));

// Initial setup
loadDataFromDatabase();
updateClock();
window.setInterval(updateClock, 1000);
