<?php
session_start();
require_once __DIR__ . '/db.php';

// Agar admin login nahi hai to login.php par redirect karein
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="ps">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="AL NASRAT FOODS - Factory dashboard for inventory, khata, wasoli, and bakaya management in Kandahar." />
    <meta name="theme-color" content="#121212" />
    <title>AL NASRAT FOODS | النصرت فوڈز</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#121212',
                        panel: '#1a1a1a',
                        gold: '#d9a441',
                        warm: '#f0c876',
                        muted: '#8d8b84',
                        soft: '#ebe5d8',
                    },
                    boxShadow: {
                        glow: '0 18px 40px rgba(217,164,65,0.15)',
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --bg: #121212;
            --bg-soft: #171716;
            --panel: rgba(27, 27, 27, 0.88);
            --panel-strong: rgba(32, 31, 30, 0.92);
            --line: rgba(255, 255, 255, 0.08);
            --line-strong: rgba(255, 255, 255, 0.12);
            --text: #f2efe9;
            --text-soft: #bebab1;
            --muted: #8b8a84;
            --gold: #d9a441;
            --gold-soft: rgba(217, 164, 65, 0.12);
            --green: #6ec89d;
            --blue: #6ea9d8;
            --amber: #e4a85b;
            --red: #e68d7a;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a {
            text-decoration: none;
        }

        button,
        input,
        select {
            font: inherit;
        }

        input,
        select {
            color: var(--text);
        }

        ::selection {
            background: rgba(217, 164, 65, 0.3);
            color: #fff;
        }

        .glass {
            background: linear-gradient(180deg, rgba(31, 31, 30, 0.94), rgba(22, 22, 22, 0.9));
            border: 1px solid var(--line);
            box-shadow: 0 18px 35px rgba(0, 0, 0, 0.18), inset 0 1px rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(18px);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            padding: 0 12px;
            border-radius: 12px;
            color: #a9a59e;
            font-size: 11px;
            border: 1px solid transparent;
            transition: .18s ease;
        }

        .nav-item:hover {
            color: #f0ece5;
            background: rgba(255, 255, 255, 0.03);
        }

        .nav-item.active {
            color: #f2d58f;
            background: linear-gradient(90deg, rgba(217, 164, 65, 0.12), rgba(217, 164, 65, 0.04));
            border-color: rgba(217, 164, 65, 0.12);
            position: relative;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -18px;
            top: 9px;
            width: 2px;
            height: 20px;
            border-radius: 999px;
            background: var(--gold);
        }

        .nav-label {
            display: flex;
            flex-direction: column;
            gap: 1px;
            line-height: 1.15;
        }

        .english-inline,
        .english-caption {
            color: #817d75;
            font-size: 8px;
            font-weight: 500;
            letter-spacing: .02em;
            line-height: 1.25;
            text-transform: none;
        }

        .english-inline {
            display: block;
        }

        .english-caption {
            display: block;
            margin-top: 3px;
        }

        .table-btn .english-inline {
            color: inherit;
            opacity: .7;
        }

        .bilingual-button {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .field-label .english-inline {
            margin-top: 3px;
            font-size: 8px;
        }

        .nav-badge {
            margin-left: auto;
            padding: 2px 6px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: #d4d1ca;
            font-size: 9px;
            font-family: 'DM Mono', monospace;
        }

        .brand-mark {
            background: linear-gradient(135deg, #f4d27d, #c88c2d);
            color: #1d170d;
        }

        .live-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #60c88d;
            box-shadow: 0 0 10px rgba(96, 200, 141, 0.6);
        }

        .metric-card {
            min-height: 170px;
            transition: transform .18s ease, border-color .18s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .metric-label {
            color: #a4a09c;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .metric-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .metric-icon svg {
            width: 15px;
            height: 15px;
        }

        .metric-value {
            font-family: 'DM Mono', monospace;
            font-size: clamp(22px, 2vw, 28px);
            letter-spacing: -.06em;
            font-weight: 500;
        }

        .sparkline {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 24px;
        }

        .sparkline span {
            display: block;
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(180deg, rgba(217, 164, 65, 0.9), rgba(217, 164, 65, 0.12));
        }

        .search-wrap {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 10px;
            height: 34px;
        }

        .search-wrap input {
            width: 140px;
            background: transparent;
            border: 0;
            outline: none;
            color: var(--text);
            font-size: 11px;
        }

        .search-wrap input::placeholder {
            color: #75736f;
        }

        .table-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 12px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(217, 164, 65, 0.16);
            background: rgba(217, 164, 65, 0.05);
            color: var(--gold);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .table-btn.secondary {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            color: #e7e4df;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        thead th {
            padding: 12px 14px;
            text-align: left;
            color: #7c7a75;
            font-size: 9px;
            letter-spacing: .18em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        tbody td {
            padding: 14px;
            color: #dfe0db;
            font-size: 11px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.015);
        }

        .item-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .item-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 15px;
        }

        .mini-label {
            display: block;
            color: #86837d;
            font-size: 8px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .stock-qty {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            letter-spacing: -.03em;
            color: #efeae1;
        }

        .stock-qty.low {
            color: #efb86b;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 8px;
            border-radius: 999px;
            padding: 5px 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.02);
            color: #dbd6cf;
        }

        .pill::before {
            content: '';
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--green);
        }

        .pill.warning {
            border-color: rgba(228, 168, 91, 0.18);
            background: rgba(228, 168, 91, 0.06);
            color: #efb96c;
        }

        .pill.warning::before {
            background: var(--amber);
        }

        .quick-btn {
            width: 29px;
            height: 29px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            color: #aaa5a0;
            transition: .15s ease;
        }

        .quick-btn:hover {
            border-color: rgba(217, 164, 65, 0.2);
            color: var(--gold);
            background: rgba(217, 164, 65, 0.06);
        }

        .quick-btn.delete:hover {
            border-color: rgba(230, 141, 122, 0.2);
            color: #f29d7d;
            background: rgba(230, 141, 122, 0.08);
        }

        .quick-btn svg {
            width: 13px;
            height: 13px;
        }

        .modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.58);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 50;
        }

        .hidden {
            display: none !important;
        }

        .modal-panel {
            width: min(100%, 420px);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(21, 21, 20, 0.98), rgba(18, 18, 18, 0.96));
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 22px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.32);
        }

        .field-label {
            display: block;
            margin-top: 16px;
            font-size: 9px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #7c7a75;
        }

        .input,
        .select,
        .textarea {
            width: 100%;
            margin-top: 8px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            color: var(--text);
            padding: 11px 12px;
            outline: none;
        }

        .input:focus,
        .select:focus,
        .textarea:focus {
            border-color: rgba(217, 164, 65, 0.38);
            box-shadow: 0 0 0 3px rgba(217, 164, 65, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .primary-btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #f0c66f, #d59d3d);
            color: #1b140c;
            font-weight: 700;
            padding: 12px 16px;
            box-shadow: 0 10px 25px rgba(217, 164, 65, 0.12);
        }

        .secondary-btn {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.02);
            color: #e8e3dc;
            padding: 12px 16px;
            font-weight: 600;
        }

        .toast {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 80;
            min-width: 220px;
            max-width: 320px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(26, 26, 26, 0.96);
            border: 1px solid rgba(109, 200, 125, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f2efe9;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.2);
        }

        .toast.error {
            border-color: rgba(230, 141, 122, 0.2);
        }

        .toast-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            background: rgba(110, 200, 157, 0.12);
            color: #a4efbf;
        }

        .toast.error .toast-icon {
            background: rgba(230, 141, 122, 0.12);
            color: #f6b19f;
        }

        .small-amount {
            font-family: 'DM Mono', monospace;
        }

        @media (max-width: 1024px) {
            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                bottom: 0;
                z-index: 40;
                transition: left .2s ease;
            }

            .sidebar.open {
                left: 0;
            }
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-panel {
                padding: 18px;
            }
        }
    </style>
</head>

<body class="bg-[#121212] text-[#f5f1e9] antialiased">
    <div
        class="fixed left-[15%] top-[-180px] h-[360px] w-[360px] rounded-full bg-[#d9a441] opacity-[0.06] blur-[100px]">
    </div>
    <div
        class="fixed bottom-[-160px] right-[-80px] h-[360px] w-[360px] rounded-full bg-[#d9a441] opacity-[0.06] blur-[100px]">
    </div>

    <div class="mx-auto flex max-w-[1600px]">
        <aside id="sidebar"
            class="sidebar glass fixed left-0 top-0 z-40 h-screen w-[245px] px-5 py-6 lg:sticky lg:block">
            <div class="flex items-center gap-3">
                <div class="brand-mark flex h-11 w-11 items-center justify-center rounded-2xl font-black text-[15px]">AN
                </div>
                <div>
                    <p class="text-[10px] font-bold tracking-[0.28em] text-white">AL NASRAT</p>
                    <p class="mt-1 text-[9px] font-semibold tracking-[0.22em] text-[#d9a441]">FOODS · KANDAHAR</p>
                </div>
            </div>

            <div class="mt-10">
                <p class="px-3 text-[9px] font-semibold tracking-[0.2em] text-[#76736d] uppercase">د کار <span
                        class="english-inline">Workspace</span></p>
                <nav class="mt-3 space-y-1">
                    <a class="nav-item active" href="#overview"><span>◫</span> <span class="nav-label">د خلاص <small
                                class="english-inline">Overview</small></span></a>
                    <a class="nav-item" href="#inventory"><span>▣</span> <span class="nav-label">د خامو موادو لیست
                            <small class="english-inline">Inventory</small></span><span class="nav-badge"
                            id="navInventoryCount">08</span></a>
                    <a class="nav-item" href="#khata"><span>▤</span> <span class="nav-label">د حساب کتاب <small
                                class="english-inline">Daily ledger</small></span></a>
                    <a class="nav-item" href="#wasoli"><span>◌</span> <span class="nav-label">وصولي <small
                                class="english-inline">Collections</small></span></a>
                    <a class="nav-item" href="#bakaya"><span>◍</span> <span class="nav-label">بقایا جات <small
                                class="english-inline">Outstanding</small></span></a>
                </nav>
            </div>

            <div class="mt-auto pt-8">
                <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                    <div class="flex items-center gap-2 text-[11px] text-[#dfe7df]">
                        <span class="live-dot"></span>
                        <span>د فابریک فعال <small class="english-inline">Factory active</small></span>
                    </div>
                    <p class="mt-2 text-[10px] leading-5 text-[#817f79]">د ښه وخت په پیل کې ښه خامو مواد.</p>
                </div>
                <div class="mt-5 flex items-center justify-between border-t border-white/10 pt-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#d9a441]/15 text-[11px] font-bold text-[#f0cf81] border border-[#d9a441]/30">
                            👑
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-[#f0ece6]"><?= htmlspecialchars($admin_name) ?></p>
                            <p class="text-[9px] text-[#7c7a75]">اداره کوونکی (Admin)</p>
                        </div>
                    </div>
                    <a href="logout.php" title="وتل (Logout)" class="p-2 rounded-lg bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 text-[10px] transition font-semibold">
                        وتل ⎋
                    </a>
                </div>
            </div>
        </aside>

        <main id="overview" class="min-w-0 flex-1 px-5 pb-10 pt-5 sm:px-8 lg:px-10">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-5">
                <div class="flex items-center gap-3">
                    <button id="menuToggle"
                        class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/[0.02] text-[#b9b5af]"
                        aria-label="Toggle menu">☰</button>
                    <div>
                        <p class="text-[10px] font-semibold tracking-[0.2em] text-[#7d7a73] uppercase">د مدیریت <span
                                class="english-inline">Management</span></p>
                        <h1 class="mt-1 text-[18px] font-semibold text-white sm:text-[20px]">AL NASRAT FOODS <span
                                class="mx-1 text-[#595550]">|</span> <span class="text-[#d9a441]">النصرت فوڈز</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden items-center gap-2 sm:flex">
                        <span class="live-dot"></span>
                        <span class="text-[10px] text-[#bbb5ad]">د عملیات فعال <small class="english-inline">Operations
                                live</small></span>
                    </div>
                    <div class="text-right">
                        <p id="localTime" class="font-mono text-[12px] text-[#f0ece5]">--:--:--</p>
                        <p class="mt-1 text-[9px] text-[#7d7a73]">کندهار · <span class="english-inline">Kandahar time ·
                                UTC+4:30</span></p>
                    </div>
                    <div class="relative">
                        <select id="currencySelect"
                            class="appearance-none rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 pr-8 text-[11px] font-semibold text-[#f0ece5] outline-none">
                            <option value="AFN">AFN ؋</option>
                            <option value="PKR">PKR ₨</option>
                            <option value="USD">USD $</option>
                        </select>
                        <span
                            class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[#7d7a73]">▾</span>
                    </div>
                </div>
            </header>

            <section class="mt-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <div
                        class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#d9a441]">
                        <span class="h-px w-5 bg-[#d9a441]"></span>
                        <span id="greetingText">د وخت په بار</span>
                    </div>
                    <h2 class="mt-2 text-[28px] font-semibold tracking-[-0.05em] text-white sm:text-[34px]">ښه ورځ،
                        <span class="text-[#d9a441]">کندهار</span></h2>
                    <p class="mt-2 text-[11px] text-[#827f79]">د فابریک د نن ورځې وضعیت</p>
                </div>
                <button id="stockActionBtn"
                    class="rounded-xl bg-gradient-to-r from-[#f0c76d] to-[#d9a441] px-4 py-2.5 text-[11px] font-bold text-[#17130b] shadow-glow"><span
                        class="bilingual-button">+ د مال راتلل / د مال وتل <small class="english-inline">Stock in /
                            out</small></span></button>
            </section>

            <section class="mt-7 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <article class="glass metric-card rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="metric-label">د نن ورځې فروش <span class="english-inline">Today’s
                                sales</span></span>
                        <span class="metric-icon"
                            style="background: rgba(217,164,65,0.08); border-color: rgba(217,164,65,0.16); color: #e6c26b;">↗</span>
                    </div>
                    <p class="metric-value mt-4 text-[#f2efe9]" data-money="124850">؋124,850</p>
                    <p class="mt-2 text-[10px] text-[#7d7a73]"><span class="text-[#76d3a3]">↑ 8.2%</span> په پرمخ</p>
                    <div class="sparkline mt-4" aria-hidden="true">
                        <span style="height:30%"></span><span style="height:42%"></span><span
                            style="height:35%"></span><span style="height:58%"></span><span
                            style="height:44%"></span><span style="height:68%"></span><span
                            style="height:52%"></span><span style="height:82%"></span><span
                            style="height:60%"></span><span style="height:95%"></span><span
                            style="height:76%"></span><span style="height:100%"></span>
                    </div>
                </article>

                <article class="glass metric-card rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="metric-label">د وصول شوي <span class="english-inline">Payments
                                collected</span></span>
                        <span class="metric-icon"
                            style="background: rgba(110,200,157,0.09); border-color: rgba(110,200,157,0.14); color: #88d4aa;">◫</span>
                    </div>
                    <p class="metric-value mt-4 text-[#f2efe9]" data-money="89600">؋89,600</p>
                    <p class="mt-2 text-[10px] text-[#7d7a73]">د <span class="text-[#e5e0d8]">12</span> عملیاتو له لارې
                    </p>
                    <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/5">
                        <div class="h-full w-[72%] rounded-full bg-[#6ec89d]"></div>
                    </div>
                    <p class="mt-2 text-[9px] text-[#7d7a73]">72% د نن ورځې له فروش څخه</p>
                </article>

                <article class="glass metric-card rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="metric-label">بقایا جات <span class="english-inline">Outstanding
                                balance</span></span>
                        <span class="metric-icon"
                            style="background: rgba(228,168,91,0.08); border-color: rgba(228,168,91,0.14); color: #e9b465;">◍</span>
                    </div>
                    <p id="bakayaMetricValue" class="metric-value mt-4 text-[#f2efe9]">؋384,200</p>
                    <p class="mt-2 text-[10px] text-[#7d7a73]">د <span id="customerCount"
                            class="text-[#e5e0d8]">8</span> فعالو خریدارانو څخه</p>
                    <div id="customerAvatarRow" class="mt-4 flex -space-x-2"></div>
                </article>

                <article class="glass metric-card rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="metric-label">د خامو موادو موجودی <span class="english-inline">Raw material
                                stock</span></span>
                        <span class="metric-icon"
                            style="background: rgba(110,169,216,0.08); border-color: rgba(110,169,216,0.14); color: #8bb7d9;">▣</span>
                    </div>
                    <p class="metric-value mt-4 text-[#f2efe9]"><span id="inventoryTotalMetric">11,550</span> <span
                            class="text-sm text-[#7d7a73]">kg</span></p>
                    <p class="mt-2 text-[10px] text-[#7d7a73]"><span id="lowStockMetric" class="text-[#efb86b]">2</span>
                        کم موجودی</p>
                    <div class="mt-4 flex items-end gap-1.5 h-[26px]" aria-hidden="true">
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:72%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:52%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:81%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:36%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:64%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:48%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:89%"></span>
                        <span class="block flex-1 rounded-t-[6px] bg-[#7ea9d4]/80" style="height:28%"></span>
                    </div>
                </article>
            </section>

            <section id="inventory" class="glass mt-5 overflow-hidden rounded-2xl">
                <div class="flex flex-wrap items-center justify-between gap-4 px-5 pb-4 pt-5 sm:px-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-[15px] font-semibold text-[#f0ece5]">د خامو موادو لیست <span
                                    class="english-caption">Raw material inventory</span></h3>
                            <span
                                class="rounded-md border border-white/10 bg-white/[0.03] px-1.5 py-0.5 text-[8px] uppercase text-[#8b8a84]">Live</span>
                        </div>
                        <p class="mt-1 text-[10px] text-[#7d7a73]">د خامو موادو د لورولو او کمیدو کنټرول</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="search-wrap">
                            <span class="text-[#7d7a73]">⌕</span>
                            <input id="inventorySearch" type="search" placeholder="د توکو پلټل"
                                aria-label="Search inventory" />
                        </label>
                        <button id="addMaterialBtn" class="table-btn" type="button"><span class="bilingual-button">+ د
                                توکي اضافه <small class="english-inline">Add item</small></span></button>
                        <button id="stockModalBtn" class="table-btn secondary" type="button"><span
                                class="bilingual-button">د مال راتلل <small class="english-inline">Stock in /
                                    out</small></span></button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th class="pl-5 sm:pl-6">د توکي نوم <span class="english-inline">Item</span></th>
                                <th>موجودي <span class="english-inline">On hand</span></th>
                                <th>حالت <span class="english-inline">Status</span></th>
                                <th>د آخر حرکت <span class="english-inline">Last movement</span></th>
                                <th class="pr-5 text-right sm:pr-6">عمل <span class="english-inline">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody"></tbody>
                    </table>
                </div>

                <div
                    class="flex flex-wrap items-center justify-between gap-2 border-t border-white/10 px-5 py-3.5 sm:px-6">
                    <p id="inventorySummary" class="text-[9px] text-[#7d7a73]">د 8 خامو موادو ښودل</p>
                    <p class="text-[9px] text-[#6f6b67]">د حرکت ثبت د وسیله په حافظه کې دی</p>
                </div>
            </section>

            <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                <article id="khata" class="glass rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 px-5 py-4 sm:px-6">
                        <div>
                            <h3 class="text-[15px] font-semibold text-[#f0ece5]">د حساب کتاب <span
                                    class="english-caption">Daily ledger</span></h3>
                            <p class="mt-1 text-[10px] text-[#7d7a73]">د ورځني کتاب او د خرچونو ثبت</p>
                        </div>
                        <button id="addKhataBtn" class="table-btn" type="button"><span class="bilingual-button">+ اضافه
                                <small class="english-inline">Add entry</small></span></button>
                    </div>
                    <div class="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th class="pl-5 sm:pl-6">تاریخ <span class="english-inline">Date</span></th>
                                    <th>تفصیل <span class="english-inline">Details</span></th>
                                    <th>مقدار <span class="english-inline">Amount</span></th>
                                    <th class="pr-5 sm:pr-6">نوع <span class="english-inline">Type</span></th>
                                </tr>
                            </thead>
                            <tbody id="khataTableBody"></tbody>
                        </table>
                    </div>
                </article>

                <article id="wasoli" class="glass rounded-2xl overflow-hidden">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 px-5 py-4 sm:px-6">
                        <div>
                            <h3 class="text-[15px] font-semibold text-[#f0ece5]">وصولي <span
                                    class="english-caption">Recent collections</span></h3>
                            <p class="mt-1 text-[10px] text-[#7d7a73]">د وصول شوي پیسو ثبت</p>
                        </div>
                        <button id="addWasoliBtn" class="table-btn" type="button"><span class="bilingual-button">+ اضافه
                                <small class="english-inline">Add collection</small></span></button>
                    </div>
                    <div class="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th class="pl-5 sm:pl-6">تاریخ <span class="english-inline">Date</span></th>
                                    <th>خریدار <span class="english-inline">Customer</span></th>
                                    <th>مقدار <span class="english-inline">Amount</span></th>
                                    <th class="pr-5 sm:pr-6">وضعیت <span class="english-inline">Status</span></th>
                                </tr>
                            </thead>
                            <tbody id="wasoliTableBody"></tbody>
                        </table>
                    </div>
                </article>
            </section>

            <section id="bakaya" class="glass mt-5 overflow-hidden rounded-2xl">
                <div
                    class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 px-5 py-4 sm:px-6">
                    <div>
                        <h3 class="text-[15px] font-semibold text-[#f0ece5]">بقایا جات <span
                                class="english-caption">Customer balances</span></h3>
                        <p class="mt-1 text-[10px] text-[#7d7a73]">د نه تادیه شوي بېرېکونه حسابونه</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="addCustomerBtn" class="table-btn" type="button"><span class="bilingual-button">+ د
                                خریدار اضافه <small class="english-inline">Add customer</small></span></button>
                        <div class="text-right">
                            <p class="text-[9px] uppercase tracking-[0.14em] text-[#7d7a73]">د مجموعي قسط <span
                                    class="english-inline">Total receivable</span></p>
                            <p id="bakayaTotal" class="small-amount mt-1 text-[18px] text-[#d9a441]">؋384,200</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th class="pl-5 sm:pl-6">خریدار <span class="english-inline">Customer</span></th>
                                <th>د وروستي تادی <span class="english-inline">Last payment</span></th>
                                <th>بقایا <span class="english-inline">Balance</span></th>
                                <th>حالت <span class="english-inline">Status</span></th>
                                <th class="pr-5 text-right sm:pr-6">عمل <span class="english-inline">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody id="bakayaTableBody"></tbody>
                    </table>
                </div>
            </section>

            <footer class="flex flex-wrap items-center justify-between gap-2 pb-2 pt-6 text-[9px] text-[#736f68]">
                <p>© 2026 AL NASRAT FOODS · د کندهار په فابریک کې جوړ شوې</p>
                <p>د قیمت حالت: <span id="footerCurrency" class="text-[#bfa46f]">AFN</span> · د کندهار وخت</p>
            </footer>
        </main>
    </div>

    <div id="stockModal" class="modal-bg hidden" aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="stockModalTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#7d7a73]">د خامو موادو لیست
                        <span class="english-inline">Inventory</span></p>
                    <h2 id="stockModalTitle" class="mt-2 text-[20px] font-semibold text-white">د مال راتلل / د مال وتل
                        <span class="english-caption">Stock in / out</span></h2>
                </div>
                <button type="button" id="closeStockModal"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/[0.02] text-[#b9b5af]">✕</button>
            </div>

            <form id="stockForm" class="mt-5">
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer rounded-2xl border border-white/10 bg-white/[0.02] p-3">
                        <input type="radio" name="movementType" value="in" checked class="sr-only" />
                        <span class="block text-[11px] font-semibold text-[#f0ece5]">د مال راتلل <small
                                class="english-inline">Stock in</small></span>
                        <span class="mt-1 block text-[9px] text-[#7d7a73]">موجودی زیاتول</span>
                    </label>
                    <label class="cursor-pointer rounded-2xl border border-white/10 bg-white/[0.02] p-3">
                        <input type="radio" name="movementType" value="out" class="sr-only" />
                        <span class="block text-[11px] font-semibold text-[#f0ece5]">د مال وتل <small
                                class="english-inline">Stock out</small></span>
                        <span class="mt-1 block text-[9px] text-[#7d7a73]">موجودی کمول</span>
                    </label>
                </div>

                <label class="field-label" for="stockItemSelect">د توکي نوم <span class="english-inline">Item
                        name</span></label>
                <select id="stockItemSelect" class="select"></select>

                <div class="form-row">
                    <div>
                        <label class="field-label" for="stockQtyInput">مقدار <span
                                class="english-inline">Quantity</span></label>
                        <input id="stockQtyInput" class="input" type="number" min="0.01" step="0.01"
                            placeholder="مثال: 500" required />
                    </div>
                    <div>
                        <label class="field-label" for="stockUnitInput">واحد <span
                                class="english-inline">Unit</span></label>
                        <input id="stockUnitInput" class="input" type="text" value="kg" readonly />
                    </div>
                </div>

                <label class="field-label" for="stockNoteInput">یادونه <span class="english-inline">Note</span></label>
                <input id="stockNoteInput" class="input" type="text" maxlength="120"
                    placeholder="مثال: د تامیناتو رسید #104" />

                <p id="stockError" class="mt-3 hidden text-[10px] text-[#ef9d8b]">Please enter valid values.</p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button id="cancelStockModal" type="button" class="secondary-btn">لغو <span
                            class="english-inline">Cancel</span></button>
                    <button type="submit" class="primary-btn">ثبت <span class="english-inline">Save</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="materialModal" class="modal-bg hidden" aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="materialModalTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#7d7a73]">د خامو موادو فهرست
                        <span class="english-inline">Inventory catalog</span></p>
                    <h2 id="materialModalTitle" class="mt-2 text-[20px] font-semibold text-white"><span
                            id="materialModalPashtoTitle">د توکي اضافه</span><span id="materialModalEnglishTitle"
                            class="english-caption">Add material</span></h2>
                </div>
                <button type="button" id="closeMaterialModal"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/[0.02] text-[#b9b5af]">✕</button>
            </div>

            <form id="materialForm" class="mt-5">
                <input id="materialId" type="hidden" />
                <label class="field-label" for="materialName">د توکي نوم <span class="english-inline">Pashto
                        name</span></label>
                <input id="materialName" class="input" type="text" maxlength="80" placeholder="مثال: زیتون" required />

                <label class="field-label" for="materialEnglishName">انګلیسي نوم <span class="english-inline">English
                        name (optional)</span></label>
                <input id="materialEnglishName" class="input" type="text" maxlength="80"
                    placeholder="e.g. Sunflower oil" />

                <label class="field-label" for="materialCategory">ډول <span
                        class="english-inline">Category</span></label>
                <input id="materialCategory" class="input" type="text" maxlength="60" placeholder="مثال: د خوراک روغتی"
                    required />

                <div class="form-row">
                    <div>
                        <label class="field-label" for="materialQty">د پیل موجودي <span class="english-inline">Starting
                                stock</span></label>
                        <input id="materialQty" class="input" type="number" min="0" step="0.01" placeholder="0"
                            required />
                    </div>
                    <div>
                        <label class="field-label" for="materialAlert">د کم موجودي حد <span class="english-inline">Low
                                stock alert</span></label>
                        <input id="materialAlert" class="input" type="number" min="0" step="0.01" placeholder="0"
                            required />
                    </div>
                </div>

                <label class="field-label" for="materialUnit">واحد <span class="english-inline">Unit</span></label>
                <input id="materialUnit" class="input" type="text" maxlength="12" value="kg"
                    placeholder="kg / liter / bag" required />

                <p id="materialError" class="mt-3 hidden text-[10px] text-[#ef9d8b]">Please fill the required fields.
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button id="cancelMaterialModal" type="button" class="secondary-btn">لغو <span
                            class="english-inline">Cancel</span></button>
                    <button type="submit" class="primary-btn">ثبت <span class="english-inline">Save</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="customerModal" class="modal-bg hidden" aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="customerModalTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#7d7a73]">د خریدارانو فهرست
                        <span class="english-inline">Customer list</span></p>
                    <h2 id="customerModalTitle" class="mt-2 text-[20px] font-semibold text-white">د خریدار اضافه <span
                            class="english-caption">Add customer</span></h2>
                </div>
                <button type="button" id="closeCustomerModal"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/[0.02] text-[#b9b5af]">✕</button>
            </div>

            <form id="customerForm" class="mt-5">
                <label class="field-label" for="customerName">د خریدار نوم <span class="english-inline">Customer
                        name</span></label>
                <input id="customerName" class="input" type="text" maxlength="80" placeholder="مثال: محمد رحیم"
                    required />

                <label class="field-label" for="customerBusiness">د سوداګرۍ ډول <span class="english-inline">Business
                        type</span></label>
                <input id="customerBusiness" class="input" type="text" maxlength="60" placeholder="مثال: عمده فروش"
                    required />

                <div class="form-row">
                    <div>
                        <label class="field-label" for="customerBalance">بقایا <span
                                class="english-inline">Balance</span></label>
                        <input id="customerBalance" class="input" type="number" min="0" step="0.01" placeholder="0"
                            required />
                    </div>
                    <div>
                        <label class="field-label" for="customerLocation">ځای <span
                                class="english-inline">Location</span></label>
                        <input id="customerLocation" class="input" type="text" maxlength="50" placeholder="کندهار"
                            required />
                    </div>
                </div>

                <label class="field-label" for="customerStatus">حالت <span class="english-inline">Status</span></label>
                <select id="customerStatus" class="select">
                    <option value="Current">حاضر</option>
                    <option value="Due soon">وروسته</option>
                    <option value="Overdue">د تادیې لپاره</option>
                </select>

                <p id="customerError" class="mt-3 hidden text-[10px] text-[#ef9d8b]">Please fill the required fields.
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button id="cancelCustomerModal" type="button" class="secondary-btn">لغو <span
                            class="english-inline">Cancel</span></button>
                    <button type="submit" class="primary-btn">ثبت <span class="english-inline">Save</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="ledgerModal" class="modal-bg hidden" aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="ledgerModalTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#7d7a73]">د حساب کتاب <span
                            class="english-inline">Daily ledger</span></p>
                    <h2 id="ledgerModalTitle" class="mt-2 text-[20px] font-semibold text-white">د نوي ثبت <span
                            class="english-caption">Add ledger entry</span></h2>
                </div>
                <button type="button" id="closeLedgerModal"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/[0.02] text-[#b9b5af]">✕</button>
            </div>

            <form id="ledgerForm" class="mt-5">
                <label class="field-label" for="ledgerDate">تاریخ <span class="english-inline">Date</span></label>
                <input id="ledgerDate" class="input" type="date" required />

                <label class="field-label" for="ledgerTitle">تفصیل <span
                        class="english-inline">Description</span></label>
                <input id="ledgerTitle" class="input" type="text" maxlength="80" placeholder="مثال: د روغتي د خرید"
                    required />

                <div class="form-row">
                    <div>
                        <label class="field-label" for="ledgerAmount">مقدار <span
                                class="english-inline">Amount</span></label>
                        <input id="ledgerAmount" class="input" type="number" min="0" step="0.01" placeholder="0"
                            required />
                    </div>
                    <div>
                        <label class="field-label" for="ledgerType">نوع <span class="english-inline">Type</span></label>
                        <select id="ledgerType" class="select">
                            <option value="credit">کریډیټ</option>
                            <option value="debit">ډیبیت</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button id="cancelLedgerModal" type="button" class="secondary-btn">لغو <span
                            class="english-inline">Cancel</span></button>
                    <button type="submit" class="primary-btn">ثبت <span class="english-inline">Save</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast hidden" role="status" aria-live="polite">
        <span class="toast-icon">✓</span>
        <span id="toastMessage">Saved</span>
    </div>

    <script>
        const storageKeys = {
            inventory: 'al-nasrat-foods-inventory-v1',
            customers: 'al-nasrat-foods-customers-v1',
            khata: 'al-nasrat-foods-khata-v1',
            wasoli: 'al-nasrat-foods-wasoli-v1',
            currency: 'al-nasrat-foods-currency-v1',
        };

        const defaultInventory = [
            { id: crypto.randomUUID(), name: 'روغتي', englishName: 'Sunflower oil', category: 'د خوراک روغتي', icon: '🫒', quantity: 2450, minimum: 700, unit: 'kg', movement: 'د مال راتلل · 500 kg', time: 'نن، 09:42' },
            { id: crypto.randomUUID(), name: 'غنمي اوړه', englishName: 'Wheat flour', category: 'غله', icon: '🌾', quantity: 3800, minimum: 1000, unit: 'kg', movement: 'د مال وتل · 120 kg', time: 'نن، 09:15' },
            { id: crypto.randomUUID(), name: 'سپین بوره', englishName: 'White sugar', category: 'بوره', icon: '◈', quantity: 1820, minimum: 500, unit: 'kg', movement: 'د مال راتلل · 400 kg', time: 'نن، 08:50' },
            { id: crypto.randomUUID(), name: 'سویا روغتي', englishName: 'Soybean oil', category: 'د خوراک روغتي', icon: '🫙', quantity: 960, minimum: 500, unit: 'kg', movement: 'د مال وتل · 50 kg', time: 'نن، 08:34' },
            { id: crypto.randomUUID(), name: 'وریجې', englishName: 'Basmati rice', category: 'غله', icon: '🍚', quantity: 1240, minimum: 400, unit: 'kg', movement: 'د مال راتلل · 250 kg', time: 'پرون، 16:20' },
            { id: crypto.randomUUID(), name: 'مالګه', englishName: 'Refined salt', category: 'مصالحې', icon: '✳', quantity: 430, minimum: 350, unit: 'kg', movement: 'د مال وتل · 25 kg', time: 'پرون، 15:08' },
            { id: crypto.randomUUID(), name: 'د رومي بانجانو پیسټ', englishName: 'Tomato paste', category: 'خام مواد', icon: '🍅', quantity: 620, minimum: 250, unit: 'kg', movement: 'د مال راتلل · 100 kg', time: 'پرون، 13:45' },
            { id: crypto.randomUUID(), name: 'د بسته بندۍ فلم', englishName: 'Packaging film', category: 'بسته بندي', icon: '▤', quantity: 230, minimum: 300, unit: 'kg', movement: 'د مال وتل · 80 kg', time: 'پرون، 11:30' },
        ];

        const defaultCustomers = [
            { id: crypto.randomUUID(), name: 'محمد رحیم', business: 'عمده فروش', location: 'کندهار ښار', initials: 'MR', status: 'Overdue', lastPayment: '05 اکتوبر', balance: 86500 },
            { id: crypto.randomUUID(), name: 'فرید او کورنۍ', business: 'توزیع', location: 'سپین Boldak', initials: 'FS', status: 'Due soon', lastPayment: '07 اکتوبر', balance: 72300 },
            { id: crypto.randomUUID(), name: 'احمد خان', business: 'عمده فروش', location: 'کندهار ښار', initials: 'AK', status: 'Overdue', lastPayment: '04 اکتوبر', balance: 58400 },
            { id: crypto.randomUUID(), name: 'نوید سپر مارکیټ', business: 'پرچون', location: 'آینو مينا', initials: 'NS', status: 'Current', lastPayment: '08 اکتوبر', balance: 42500 },
            { id: crypto.randomUUID(), name: 'عبدالباسط', business: 'پرچون', location: 'کندهار ښار', initials: 'AB', status: 'Overdue', lastPayment: '03 اکتوبر', balance: 38500 },
            { id: crypto.randomUUID(), name: 'مالک ټریډینگ', business: 'توزیع', location: 'دمن', initials: 'MT', status: 'Due soon', lastPayment: '09 اکتوبر', balance: 34000 },
            { id: crypto.randomUUID(), name: 'سردار مارکیټ', business: 'عمده فروش', location: 'کندهار ښار', initials: 'SM', status: 'Current', lastPayment: '06 اکتوبر', balance: 27000 },
            { id: crypto.randomUUID(), name: 'حبیب فوڈز', business: 'پرچون', location: 'سپین Boldak', initials: 'HF', status: 'Overdue', lastPayment: '02 اکتوبر', balance: 25000 },
        ];

        const defaultKhata = [
            { date: '2026-10-10', title: 'د خامو موادو خرڅ', amount: 32000, type: 'debit' },
            { date: '2026-10-10', title: 'د پدېویل ډیلیت', amount: 18000, type: 'credit' },
            { date: '2026-10-09', title: 'د برنج د سپکو', amount: 22000, type: 'debit' },
        ];

        const defaultWasoli = [
            { date: '2026-10-10', customer: 'محمد رحیم', amount: 25000, status: 'رسید' },
            { date: '2026-10-09', customer: 'احمد خان', amount: 18500, status: 'منتظر' },
            { date: '2026-10-08', customer: 'نوید سپر', amount: 43000, status: 'رسید' },
        ];

        const exchangeRates = { AFN: 1, PKR: 0.99, USD: 0.0142 };
        const currencyMeta = {
            AFN: { locale: 'fa-AF-u-nu-latn', code: 'AFN' },
            PKR: { locale: 'ur-PK-u-nu-latn', code: 'PKR' },
            USD: { locale: 'en-US', code: 'USD' },
        };

        let inventory = loadFromStorage(storageKeys.inventory, defaultInventory);
        let customers = loadFromStorage(storageKeys.customers, defaultCustomers);
        let khata = loadFromStorage(storageKeys.khata, defaultKhata);
        let wasoli = loadFromStorage(storageKeys.wasoli, defaultWasoli);
        let activeCurrency = localStorage.getItem(storageKeys.currency) || 'AFN';

        const inventoryTableBody = document.getElementById('inventoryTableBody');
        const inventorySummary = document.getElementById('inventorySummary');
        const inventorySearch = document.getElementById('inventorySearch');
        const inventoryTotalMetric = document.getElementById('inventoryTotalMetric');
        const lowStockMetric = document.getElementById('lowStockMetric');
        const navInventoryCount = document.getElementById('navInventoryCount');
        const currencySelect = document.getElementById('currencySelect');
        const footerCurrency = document.getElementById('footerCurrency');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const khataTableBody = document.getElementById('khataTableBody');
        const wasoliTableBody = document.getElementById('wasoliTableBody');
        const bakayaTableBody = document.getElementById('bakayaTableBody');
        const bakayaTotal = document.getElementById('bakayaTotal');
        const bakayaMetricValue = document.getElementById('bakayaMetricValue');
        const customerCount = document.getElementById('customerCount');
        const customerAvatarRow = document.getElementById('customerAvatarRow');
        const localTime = document.getElementById('localTime');
        const greetingText = document.getElementById('greetingText');
        const stockModal = document.getElementById('stockModal');
        const stockItemSelect = document.getElementById('stockItemSelect');
        const stockQtyInput = document.getElementById('stockQtyInput');
        const stockUnitInput = document.getElementById('stockUnitInput');
        const stockNoteInput = document.getElementById('stockNoteInput');
        const stockError = document.getElementById('stockError');
        const materialError = document.getElementById('materialError');
        const customerError = document.getElementById('customerError');
        const materialModal = document.getElementById('materialModal');
        const customerModal = document.getElementById('customerModal');
        const ledgerModal = document.getElementById('ledgerModal');
        const materialForm = document.getElementById('materialForm');
        const customerForm = document.getElementById('customerForm');
        const ledgerForm = document.getElementById('ledgerForm');
        const stockForm = document.getElementById('stockForm');
        const materialIdInput = document.getElementById('materialId');
        const materialNameInput = document.getElementById('materialName');
        const materialEnglishNameInput = document.getElementById('materialEnglishName');
        const materialCategoryInput = document.getElementById('materialCategory');
        const materialQtyInput = document.getElementById('materialQty');
        const materialAlertInput = document.getElementById('materialAlert');
        const materialUnitInput = document.getElementById('materialUnit');
        const customerNameInput = document.getElementById('customerName');
        const customerBusinessInput = document.getElementById('customerBusiness');
        const customerBalanceInput = document.getElementById('customerBalance');
        const customerLocationInput = document.getElementById('customerLocation');
        const customerStatusInput = document.getElementById('customerStatus');
        const ledgerDateInput = document.getElementById('ledgerDate');
        const ledgerTitleInput = document.getElementById('ledgerTitle');
        const ledgerAmountInput = document.getElementById('ledgerAmount');
        const ledgerTypeInput = document.getElementById('ledgerType');

        function saveData(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (error) {
                showToast('د ذخیره کولو لپاره ستونزه پیدا شوه', true);
                return false;
            }
        }

        function loadFromStorage(key, fallback) {
            try {
                const item = localStorage.getItem(key);
                if (!item) return JSON.parse(JSON.stringify(fallback));
                const parsed = JSON.parse(item);
                return Array.isArray(parsed) ? parsed : JSON.parse(JSON.stringify(fallback));
            } catch (error) {
                return JSON.parse(JSON.stringify(fallback));
            }
        }

        function currencyValue(baseAmount) {
            const amount = Number(baseAmount) * exchangeRates[activeCurrency];
            return new Intl.NumberFormat(currencyMeta[activeCurrency].locale, {
                style: 'currency',
                currency: currencyMeta[activeCurrency].code,
                maximumFractionDigits: activeCurrency === 'USD' ? 2 : 0,
            }).format(amount);
        }

        function formatPlain(value) {
            return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Number(value || 0));
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showToast(msg, isError = false) {
            toast.classList.remove('hidden');
            toast.classList.toggle('error', isError);
            toastMessage.textContent = msg;
            const icon = toast.querySelector('.toast-icon');
            icon.textContent = isError ? '!' : '✓';
            clearTimeout(showToast.timer);
            showToast.timer = setTimeout(() => toast.classList.add('hidden'), 2800);
        }

        function getInventoryStatus(item) {
            const isLow = Number(item.quantity) <= Number(item.minimum);
            return {
                isLow,
                label: isLow ? 'کم موجودی' : 'کافي',
                tone: isLow ? 'warning' : '',
            };
        }

        function renderInventory() {
            const searchText = inventorySearch.value.trim().toLowerCase();
            const filtered = inventory.filter((item) => {
                if (!searchText) return true;
                return item.name.toLowerCase().includes(searchText) || item.category.toLowerCase().includes(searchText);
            });

            inventoryTableBody.innerHTML = filtered.map((item) => {
                const status = getInventoryStatus(item);
                return `
            <tr>
              <td class="pl-5 sm:pl-6">
                <div class="item-box">
                  <span class="item-icon">${escapeHtml(item.icon || '▣')}</span>
                  <div>
                    <span class="block text-[11px] font-semibold text-[#efeee9]">${escapeHtml(item.name)}</span>
                    <span class="mini-label mt-1">${escapeHtml(item.category)}</span>
                  </div>
                </div>
              </td>
              <td><span class="stock-qty ${status.isLow ? 'low' : ''}">${formatPlain(item.quantity)}</span><span class="text-[#7d7a73]"> ${escapeHtml(item.unit)}</span></td>
              <td><span class="pill ${status.tone}">${status.label}</span></td>
              <td>
                <span>${escapeHtml(item.movement)}</span>
                <span class="mt-1 block text-[9px] text-[#7d7a73]">${escapeHtml(item.time)}</span>
              </td>
              <td class="pr-5 text-right sm:pr-6">
                <div class="inline-flex gap-1.5">
                  <button type="button" class="quick-btn" data-action="in" data-id="${item.id}" title="Add stock">＋</button>
                  <button type="button" class="quick-btn" data-action="out" data-id="${item.id}" title="Remove stock">−</button>
                  <button type="button" class="quick-btn" data-action="edit" data-id="${item.id}" title="Edit item">✎</button>
                  <button type="button" class="quick-btn delete" data-action="delete" data-id="${item.id}" title="Delete item">🗑</button>
                </div>
              </td>
            </tr>
          `;
            }).join('');

            const lowCount = inventory.filter((item) => getInventoryStatus(item).isLow).length;
            const totalStock = inventory.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
            inventorySummary.textContent = `د ${filtered.length} خامو موادو ښودل · ${inventory.length} ټول`;
            inventoryTotalMetric.textContent = formatPlain(totalStock);
            lowStockMetric.textContent = String(lowCount);
            navInventoryCount.textContent = String(inventory.length).padStart(2, '0');
        }

        function renderKhata() {
            khataTableBody.innerHTML = khata.map((entry) => `
          <tr>
            <td class="pl-5 sm:pl-6">${escapeHtml(entry.date)}</td>
            <td>${escapeHtml(entry.title)}</td>
            <td class="small-amount ${entry.type === 'debit' ? 'text-[#f0c76d]' : 'text-[#8ce3b1]'}">${entry.type === 'debit' ? '-' : '+'}${currencyValue(entry.amount)}</td>
            <td class="pr-5 text-right sm:pr-6"><span class="pill ${entry.type === 'debit' ? 'warning' : ''}">${entry.type === 'debit' ? 'ډیبیت' : 'کریډیټ'}</span></td>
          </tr>
        `).join('');
        }

        function renderWasoli() {
            wasoliTableBody.innerHTML = wasoli.map((entry) => `
          <tr>
            <td class="pl-5 sm:pl-6">${escapeHtml(entry.date)}</td>
            <td>${escapeHtml(entry.customer)}</td>
            <td class="small-amount text-[#eae4d7]">${currencyValue(entry.amount)}</td>
            <td class="pr-5 text-right sm:pr-6"><span class="pill ${entry.status === 'منتظر' ? 'warning' : ''}">${escapeHtml(entry.status)}</span></td>
          </tr>
        `).join('');
        }

        function renderBakaya() {
            const total = customers.reduce((sum, customer) => sum + Number(customer.balance || 0), 0);
            bakayaTotal.textContent = currencyValue(total);
            bakayaMetricValue.textContent = currencyValue(total);
            customerCount.textContent = String(customers.length);

            bakayaTableBody.innerHTML = customers.map((customer) => {
                const statusClass = customer.status === 'Overdue' ? 'warning' : '';
                return `
            <tr>
              <td class="pl-5 sm:pl-6">
                <div class="flex items-center gap-3">
                  <span class="flex h-9 w-9 items-center justify-center rounded-full border border-[#d9a441]/25 bg-[#d9a441]/10 text-[10px] font-bold text-[#f0cf81]">${escapeHtml(customer.initials)}</span>
                  <div>
                    <span class="block text-[11px] font-semibold text-[#efeee9]">${escapeHtml(customer.name)}</span>
                    <span class="mt-1 block text-[9px] text-[#7d7a73]">${escapeHtml(customer.business)} · ${escapeHtml(customer.location)}</span>
                  </div>
                </div>
              </td>
              <td>${escapeHtml(customer.lastPayment)}</td>
              <td class="small-amount text-[#f0c76d]">${currencyValue(customer.balance)}</td>
              <td><span class="pill ${statusClass}">${customer.status === 'Current' ? 'اوسني' : customer.status === 'Due soon' ? 'وروسته' : 'د تادیې لپاره'}</span></td>
              <td class="pr-5 text-right sm:pr-6">
                <button type="button" class="quick-btn delete" data-customer-id="${customer.id}" title="Remove customer">🗑</button>
              </td>
            </tr>
          `;
            }).join('');

            customerAvatarRow.innerHTML = customers.slice(0, 5).map((customer) =>
                `<span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-[#121212] bg-[#2b2926] text-[8px] font-semibold text-[#f0cf81]">${escapeHtml(customer.initials)}</span>`
            ).join('');
        }

        function updateClock() {
            const now = new Date();
            localTime.textContent = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Kabul', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
            }).format(now);
            const day = new Intl.DateTimeFormat('ps-AF', { timeZone: 'Asia/Kabul', weekday: 'long', month: 'long', day: 'numeric' }).format(now);
            greetingText.textContent = day;
        }

        function openStockModal(itemId = inventory[0]?.id || '') {
            stockItemSelect.innerHTML = inventory.map((item) => `<option value="${item.id}">${escapeHtml(item.name)} · ${formatPlain(item.quantity)} ${escapeHtml(item.unit)}</option>`).join('');
            stockItemSelect.value = itemId || inventory[0]?.id || '';
            const item = inventory.find((entry) => entry.id === stockItemSelect.value) || inventory[0];
            stockUnitInput.value = item ? item.unit : 'kg';
            stockForm.reset();
            stockQtyInput.value = '';
            stockNoteInput.value = '';
            stockError.classList.add('hidden');
            stockModal.classList.remove('hidden');
            stockModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeStockModal() {
            stockModal.classList.add('hidden');
            stockModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function openMaterialModal(item = null) {
            materialForm.reset();
            if (item) {
                materialIdInput.value = item.id;
                materialNameInput.value = item.name;
                materialCategoryInput.value = item.category;
                materialQtyInput.value = item.quantity;
                materialAlertInput.value = item.minimum;
                materialUnitInput.value = item.unit;
                document.getElementById('materialModalTitle').textContent = 'د توکی بدلون';
            } else {
                materialIdInput.value = '';
                materialNameInput.value = '';
                materialCategoryInput.value = '';
                materialQtyInput.value = 0;
                materialAlertInput.value = 0;
                materialUnitInput.value = 'kg';
                document.getElementById('materialModalTitle').textContent = 'د توکی اضافه';
            }
            materialError.classList.add('hidden');
            materialModal.classList.remove('hidden');
            materialModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeMaterialModal() {
            materialModal.classList.add('hidden');
            materialModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function openCustomerModal() {
            customerForm.reset();
            customerError.classList.add('hidden');
            customerModal.classList.remove('hidden');
            customerModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeCustomerModal() {
            customerModal.classList.add('hidden');
            customerModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function openLedgerModal() {
            ledgerForm.reset();
            ledgerDateInput.value = new Date().toISOString().slice(0, 10);
            ledgerModal.classList.remove('hidden');
            ledgerModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeLedgerModal() {
            ledgerModal.classList.add('hidden');
            ledgerModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function saveInventory() { saveData(storageKeys.inventory, inventory); }
        function saveCustomers() { saveData(storageKeys.customers, customers); }
        function persistCurrency() { localStorage.setItem(storageKeys.currency, activeCurrency); }

        function onStockSubmit(event) {
            event.preventDefault();
            const itemId = stockItemSelect.value;
            const item = inventory.find((entry) => entry.id === itemId);
            const qty = Number(stockQtyInput.value);
            const type = document.querySelector('input[name="movementType"]:checked').value;

            if (!item || !Number.isFinite(qty) || qty <= 0) {
                stockError.textContent = 'مهربانی د مقدار درست اختیار کړئ.';
                stockError.classList.remove('hidden');
                return;
            }

            const updatedQty = type === 'in' ? item.quantity + qty : item.quantity - qty;
            item.quantity = Math.max(0, updatedQty);
            item.movement = `${type === 'in' ? 'د مال راتلل' : 'د مال وتل'} · ${qty} ${item.unit}`;
            item.time = `نن، ${new Date().toLocaleTimeString(['en-GB'], { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Kabul' })}`;

            if (stockNoteInput.value.trim()) {
                item.movement += ` · ${stockNoteInput.value.trim()}`;
            }

            saveInventory();
            renderInventory();
            closeStockModal();
            showToast(type === 'in' ? 'د موجودی زیاتول ثبت شو' : 'د موجودی کمول ثبت شو');
        }

        function onMaterialSubmit(event) {
            event.preventDefault();
            const payload = {
                id: materialIdInput.value || crypto.randomUUID(),
                name: materialNameInput.value.trim(),
                category: materialCategoryInput.value.trim(),
                quantity: Number(materialQtyInput.value),
                minimum: Number(materialAlertInput.value),
                unit: materialUnitInput.value.trim() || 'kg',
                icon: '▣',
                movement: 'د توکی اضافه',
                time: `نن، ${new Date().toLocaleTimeString(['en-GB'], { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Kabul' })}`,
            };

            if (!payload.name || !payload.category || !Number.isFinite(payload.quantity) || !Number.isFinite(payload.minimum)) {
                document.getElementById('materialError').classList.remove('hidden');
                return;
            }

            const existingIndex = inventory.findIndex((item) => item.id === payload.id);
            if (existingIndex >= 0) {
                inventory[existingIndex] = { ...inventory[existingIndex], ...payload };
            } else {
                inventory.push(payload);
            }

            saveInventory();
            renderInventory();
            closeMaterialModal();
            showToast(existingIndex >= 0 ? 'د توکی بدلون ثبت شو' : 'د توکی اضافه شو');
        }

        function onCustomerSubmit(event) {
            event.preventDefault();
            const payload = {
                id: crypto.randomUUID(),
                name: customerNameInput.value.trim(),
                business: customerBusinessInput.value.trim(),
                location: customerLocationInput.value.trim(),
                initials: customerNameInput.value.trim().split(' ').slice(0, 2).map((part) => part[0]?.toUpperCase() || '').join('') || 'CU',
                status: customerStatusInput.value,
                lastPayment: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Asia/Kabul' }),
                balance: Number(customerBalanceInput.value) || 0,
            };

            if (!payload.name || !payload.business || !payload.location) {
                document.getElementById('customerError').classList.remove('hidden');
                return;
            }

            customers.push(payload);
            saveCustomers();
            renderBakaya();
            closeCustomerModal();
            showToast('د خریدار اضافه شو');
        }

        function onLedgerSubmit(event) {
            event.preventDefault();
            const entry = {
                date: ledgerDateInput.value || new Date().toISOString().slice(0, 10),
                title: ledgerTitleInput.value.trim(),
                amount: Number(ledgerAmountInput.value) || 0,
                type: ledgerTypeInput.value,
            };

            if (!entry.title || entry.amount <= 0) {
                return;
            }

            khata.unshift(entry);
            saveData(storageKeys.khata, khata);
            renderKhata();
            closeLedgerModal();
            showToast('د حساب کتاب ثبت شو');
        }

        function onWasoliAdd() {
            const customer = customers[0]?.name || 'د خریدار نوم';
            wasoli.unshift({
                date: new Date().toISOString().slice(0, 10),
                customer,
                amount: 15000,
                status: 'رسید',
            });
            saveData(storageKeys.wasoli, wasoli);
            renderWasoli();
            showToast('د وصولي ثبت شو');
        }

        document.addEventListener('click', (event) => {
            const actionTarget = event.target.closest('[data-action]');
            if (actionTarget) {
                const { action, id } = actionTarget.dataset;
                const item = inventory.find((entry) => entry.id === id);
                if (!item) return;

                if (action === 'in' || action === 'out') {
                    openStockModal(item.id);
                    const type = action === 'in' ? 'in' : 'out';
                    document.querySelectorAll('input[name="movementType"]').forEach((radio) => {
                        radio.checked = radio.value === type;
                    });
                    stockQtyInput.focus();
                }
                if (action === 'edit') openMaterialModal(item);
                if (action === 'delete') {
                    inventory = inventory.filter((entry) => entry.id !== id);
                    saveInventory();
                    renderInventory();
                    showToast('د توکی حذف شو');
                }
            }

            const customerDeleteTarget = event.target.closest('[data-customer-id]');
            if (customerDeleteTarget) {
                const id = customerDeleteTarget.dataset.customerId;
                customers = customers.filter((customer) => customer.id !== id);
                saveCustomers();
                renderBakaya();
                showToast('د خریدار حذف شو');
            }
        });

        document.getElementById('stockActionBtn').addEventListener('click', () => openStockModal());
        document.getElementById('stockModalBtn').addEventListener('click', () => openStockModal());
        document.getElementById('addMaterialBtn').addEventListener('click', () => openMaterialModal());
        document.getElementById('addCustomerBtn').addEventListener('click', () => openCustomerModal());
        document.getElementById('addKhataBtn').addEventListener('click', () => openLedgerModal());
        document.getElementById('addWasoliBtn').addEventListener('click', () => onWasoliAdd());
        document.getElementById('closeStockModal').addEventListener('click', closeStockModal);
        document.getElementById('cancelStockModal').addEventListener('click', closeStockModal);
        document.getElementById('closeMaterialModal').addEventListener('click', closeMaterialModal);
        document.getElementById('cancelMaterialModal').addEventListener('click', closeMaterialModal);
        document.getElementById('closeCustomerModal').addEventListener('click', closeCustomerModal);
        document.getElementById('cancelCustomerModal').addEventListener('click', closeCustomerModal);
        document.getElementById('closeLedgerModal').addEventListener('click', closeLedgerModal);
        document.getElementById('cancelLedgerModal').addEventListener('click', closeLedgerModal);
        document.getElementById('menuToggle').addEventListener('click', () => document.getElementById('sidebar').classList.toggle('open'));

        stockForm.addEventListener('submit', onStockSubmit);
        materialForm.addEventListener('submit', onMaterialSubmit);
        customerForm.addEventListener('submit', onCustomerSubmit);
        ledgerForm.addEventListener('submit', onLedgerSubmit);
        inventorySearch.addEventListener('input', renderInventory);
        currencySelect.addEventListener('change', (event) => {
            activeCurrency = event.target.value;
            persistCurrency();
            renderBakaya();
            renderKhata();
            renderWasoli();
            document.querySelectorAll('[data-money]').forEach((node) => {
                const value = Number(node.dataset.money || 0);
                node.textContent = currencyValue(value);
            });
            footerCurrency.textContent = activeCurrency;
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeStockModal();
                closeMaterialModal();
                closeCustomerModal();
                closeLedgerModal();
            }
        });

        document.querySelectorAll('[data-money]').forEach((el) => {
            const value = Number(el.dataset.money || 0);
            el.textContent = currencyValue(value);
        });

        function init() {
            currencySelect.value = activeCurrency;
            footerCurrency.textContent = activeCurrency;
            renderInventory();
            renderKhata();
            renderWasoli();
            renderBakaya();
            updateClock();
            setInterval(updateClock, 1000);
        }

        init();
    </script>
</body>

</html>