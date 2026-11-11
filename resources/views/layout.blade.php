<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruuy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%23000000'/%3E%3Cpath d='M18 20h28v24H18z' fill='%23ffffff'/%3E%3Cpath d='M24 20v-4h16v4' fill='%23ffffff'/%3E%3Crect x='22' y='26' width='20' height='2' fill='%23000000'/%3E%3Crect x='22' y='32' width='14' height='2' fill='%23000000'/%3E%3Crect x='22' y='38' width='10' height='2' fill='%23000000'/%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @font-face {
            font-family: 'AKbalthom Monstera';
            src: url('/fonts/AKbalthom%20Monstera.ttf') format('truetype');
        }
        body {
            background-color: #000000;
            height: 100vh;
            overflow: auto;
            font-family: 'AKbalthom Monstera', 'AKbalthom KhmerNew', 'Kantumruuy Pro', sans-serif;
        }
        .sidebar {
            flex: 0 0 70px;
            height: 100vh;
            border-right: 2px solid #444444;
            background-color: #ffffff;
            padding: 25px 10px;
            display: flex;
            flex-direction: column;
            transition: flex-basis 0.3s ease, padding 0.3s ease;
            box-sizing: border-box;
        }
        .sidebar.expanded {
            flex: 0 0 260px;
            padding: 25px 20px;
        }
        .logo-box {
            width: 40px;
            height: 40px;
            border: 2px solid #000000;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0;
            margin-bottom: 30px;
            color: #000000;
            margin-left: auto;
            margin-right: auto;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .logo-box:hover {
            transform: scale(1.1);
            background-color: #f0f0f0;
        }
        .logo-box::before {
            content: '';
            font-size: 14px;
        }
        .sidebar.expanded .logo-box {
            width: 90px;
            height: 50px;
            font-size: 18px;
            gap: 8px;
        }
        .sidebar.expanded .logo-box::before {
            content: '';
        }
        .logo-box i {
            width: 24px;
            height: 24px;
        }
        .menu-item {
            font-size: 14px;
            font-weight: 700;
            color: #000000;
            text-decoration: none;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }
        .menu-item i {
            min-width: 20px;
        }
        .menu-item span {
            display: none;
        }
        .sidebar.expanded .menu-item {
            justify-content: flex-start;
            padding: 8px 10px;
            gap: 10px;
        }
        .sidebar.expanded .menu-item span {
            display: block;
        }
        .menu-item:hover {
            color: #555555;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .main-container {
            flex: 1 1 auto;
            min-width: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .toggle-btn {
            background: #ffffff;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 4px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        table {
            table-layout: fixed;
        }
        .page-link {
            color: #000000;
            border-color: #000000;
        }
        .page-item.active .page-link {
            background-color: #000000;
            border-color: #000000;
        }
        .pagination {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar" id="sidebar">
        <div class="logo-box" onclick="toggleSidebar()" title="Toggle Sidebar"><img width="30" height="30" src="https://img.icons8.com/dotty/80/warehouse--v2.png" alt="warehouse--v2"/></div>
        <a href="{{ route('products.leaf') }}" class="menu-item"><i data-lucide="package"></i><span>ស្តុកទំនិញ</span></a>
        <a href="{{ route('products.stock') }}" class="menu-item"><i data-lucide="warehouse"></i><span>ស្តុក</span></a>
        <a href="{{ route('products.remove_stock') }}" class="menu-item"><i data-lucide="trash-2"></i><span>ដកស្តុក</span></a>
        <a href="{{ route('products.old-leaf-new-import') }}" class="menu-item"><i data-lucide="refresh-cw"></i><span>ស្តុកចាស់បញ្ចូលថ្មី</span></a>
        <a href="{{ route('products.stock.summary') }}" class="menu-item"><i data-lucide="bar-chart-2"></i><span>សង្ខេបស្តុក</span></a>
        <a href="{{ route('products.out-of-stock-table') }}" class="menu-item"><i data-lucide="alert-circle"></i><span>តារាងស្តុកអស់ស្តុក</span></a>
        <a href="{{ route('products.stock-removal-table') }}" class="menu-item"><i data-lucide="minus-circle"></i><span>តារាងស្តុកដកត្បូងប្រចាំខែ</span></a>
        <a href="{{ route('products.goods-in-stock-table') }}" class="menu-item"><i data-lucide="inbox"></i><span>តារាងទំនិញចូលស្តុក</span></a>
        <a href="{{ route('products.old-month-stock-table') }}" class="menu-item"><i data-lucide="calendar"></i><span>តារាងស្តុកខែចាស់</span></a>
    </div>

    <div class="main-container">
        <div class="main-content">
            @if(session('success') || session('error') || session('warning') || session('info'))
                <div id="session-messages" style="display:none;">
                    @if(session('success'))
                        <div data-message="{{ session('success') }}" data-type="success"></div>
                    @endif
                    @if(session('error'))
                        <div data-message="{{ session('error') }}" data-type="error"></div>
                    @endif
                    @if(session('warning'))
                        <div data-message="{{ session('warning') }}" data-type="warning"></div>
                    @endif
                    @if(session('info'))
                        <div data-message="{{ session('info') }}" data-type="info"></div>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('expanded');
    }
    lucide.createIcons();

    // Display session messages as toasts
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('session-messages');
        if (messagesContainer) {
            const messages = messagesContainer.querySelectorAll('div[data-message]');
            messages.forEach(function(msgElement) {
                const message = msgElement.getAttribute('data-message');
                const type = msgElement.getAttribute('data-type');
                if (window.showToast && message) {
                    window.showToast(message, type);
                }
            });
        }
    });
</script>
@include('partials.toasts')
@include('partials.confirm')
</body>
</html>
