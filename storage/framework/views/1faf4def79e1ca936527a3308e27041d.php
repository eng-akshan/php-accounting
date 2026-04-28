<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Accounting System'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        [data-theme="dark"] {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-sidebar: linear-gradient(180deg, #020617 0%, #0f172a 100%);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-main);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        [data-theme="dark"] .card {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .card-header {
            background-color: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .topbar {
            background: var(--bg-card);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .table {
            color: var(--text-primary);
        }

        [data-theme="dark"] .table thead th {
            color: var(--text-secondary);
            background-color: #0f172a;
        }

        [data-theme="dark"] .table tbody tr:hover {
            background-color: #1e293b;
        }

        [data-theme="dark"] .list-group-item {
            background-color: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control, 
        [data-theme="dark"] .form-select {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .btn-light {
            background-color: #334155;
            border-color: #334155;
            color: #f1f5f9;
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-primary);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: #334155;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--bg-sidebar);
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .logo {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-size: 1.25rem;
            font-weight: 600;
            border-bottom: 1px solid #334155;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 12px;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            transform: translateX(4px);
        }
        .sidebar .nav-link i { width: 20px; }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--bg-main);
            transition: background-color 0.3s ease;
        }
        .topbar {
            height: var(--topbar-height);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .stat-card {
            border-radius: 16px;
            padding: 24px;
            color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        .stat-card.gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-card.gradient-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
        .stat-card.gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-card.success { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-card.info { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .stat-card .icon { font-size: 2rem; opacity: 0.8; }
        .stat-card .value { font-size: 1.75rem; font-weight: 700; }
        .table th { font-weight: 600; color: var(--text-secondary); font-size: 0.875rem; }
        .btn-primary { background: #3b82f6; border-color: #3b82f6; }
        .btn-primary:hover { background: #2563eb; border-color: #2563eb; }
        .page-title { font-size: 1.5rem; font-weight: 600; color: var(--text-primary); }
        
        .theme-toggle {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }
        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.5);
        }
        [data-theme="dark"] .theme-toggle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        [data-theme="dark"] .theme-toggle:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        [data-theme="dark"] .text-muted {
            color: #94a3b8 !important;
        }
        [data-theme="dark"] h6, [data-theme="dark"] .h6, [data-theme="dark"] h5, [data-theme="dark"] .h5 {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-coins me-2"></i>
            Accounting
        </div>
        <nav class="nav flex-column mt-3">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <div class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#accountMenu">
                    <i class="fas fa-book"></i> Chart of Accounts
                </a>
            </div>
            <a href="<?php echo e(route('accounts.index')); ?>" class="nav-link <?php echo e(request()->routeIs('accounts.*') ? 'active' : ''); ?>">
                <i class="fas fa-landmark"></i> Accounts
            </a>
            <a href="<?php echo e(route('products.index')); ?>" class="nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                <i class="fas fa-boxes"></i> Products
            </a>
            <a href="<?php echo e(route('journal-entries.index')); ?>" class="nav-link <?php echo e(request()->routeIs('journal-entries.*') ? 'active' : ''); ?>">
                <i class="fas fa-receipt"></i> Journal Entries
            </a>
            <a href="<?php echo e(route('incomes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('incomes.*') ? 'active' : ''); ?>">
                <i class="fas fa-arrow-up"></i> Income
            </a>
            <a href="<?php echo e(route('expenses.index')); ?>" class="nav-link <?php echo e(request()->routeIs('expenses.*') ? 'active' : ''); ?>">
                <i class="fas fa-arrow-down"></i> Expenses
            </a>
            <div class="nav-item mt-3">
                <span class="nav-link text-uppercase text-muted small">Billing</span>
            </div>
            <a href="<?php echo e(route('customers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
                <i class="fas fa-users"></i> Customers
            </a>
            <a href="<?php echo e(route('quotations.index')); ?>" class="nav-link <?php echo e(request()->routeIs('quotations.*') ? 'active' : ''); ?>">
                <i class="fas fa-file-contract"></i> Quotations
            </a>
            <a href="<?php echo e(route('invoices.index')); ?>" class="nav-link <?php echo e(request()->routeIs('invoices.*') ? 'active' : ''); ?>">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
            <a href="<?php echo e(route('payments.index')); ?>" class="nav-link <?php echo e(request()->routeIs('payments.*') ? 'active' : ''); ?>">
                <i class="fas fa-credit-card"></i> Payments
            </a>
            <div class="nav-item mt-3">
                <span class="nav-link text-uppercase text-muted small">Reports</span>
            </div>
            <a href="<?php echo e(route('reports.profit-loss')); ?>" class="nav-link">
                <i class="fas fa-chart-pie"></i> Profit & Loss
            </a>
            <a href="<?php echo e(route('reports.balance-sheet')); ?>" class="nav-link">
                <i class="fas fa-scale-balanced"></i> Balance Sheet
            </a>
            <a href="<?php echo e(route('reports.cash-flow')); ?>" class="nav-link">
                <i class="fas fa-money-bill-wave"></i> Cash Flow
            </a>
            <a href="<?php echo e(route('reports.general-ledger')); ?>" class="nav-link">
                <i class="fas fa-list"></i> General Ledger
            </a>
            <div class="nav-item mt-3">
                <span class="nav-link text-uppercase text-muted small">System</span>
            </div>
            <a href="<?php echo e(route('settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <h5 class="page-title mb-0"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo e(auth()->user()->name); ?>

                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo e(route('settings.profile')); ?>" class="dropdown-item">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="p-4">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('theme');
        
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                if (newTheme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\accounting-system\resources\views/layouts/master.blade.php ENDPATH**/ ?>