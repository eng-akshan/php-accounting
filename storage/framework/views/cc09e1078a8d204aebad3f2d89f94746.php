<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card gradient-primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">$<?php echo e(number_format($totalRevenue, 2)); ?></div>
                    <div class="stat-trend <?php echo e($revenueTrend >= 0 ? 'positive' : 'negative'); ?>">
                        <i class="fas fa-caret-<?php echo e($revenueTrend >= 0 ? 'up' : 'down'); ?>"></i>
                        <?php echo e(number_format(abs($revenueTrend), 1)); ?>% vs last month
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card gradient-danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value">$<?php echo e(number_format($totalExpenses, 2)); ?></div>
                    <div class="stat-trend <?php echo e($expenseTrend <= 0 ? 'positive' : 'negative'); ?>">
                        <i class="fas fa-caret-<?php echo e($expenseTrend >= 0 ? 'up' : 'down'); ?>"></i>
                        <?php echo e(number_format(abs($expenseTrend), 1)); ?>% vs last month
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card <?php echo e($netProfit >= 0 ? 'gradient-success' : 'gradient-danger'); ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Net Profit</div>
                    <div class="stat-value">$<?php echo e(number_format($netProfit, 2)); ?></div>
                    <div class="stat-trend <?php echo e($profitTrend >= 0 ? 'positive' : 'negative'); ?>">
                        <i class="fas fa-caret-<?php echo e($profitTrend >= 0 ? 'up' : 'down'); ?>"></i>
                        <?php echo e(number_format(abs($profitTrend), 1)); ?>% vs last month
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card gradient-info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Cash Balance</div>
                    <div class="stat-value">$<?php echo e(number_format($cashBalance, 2)); ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-wallet"></i>
                        Available
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card gradient-warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Unpaid Invoices (AR)</div>
                    <div class="stat-value">$<?php echo e(number_format($receivable ?? 0, 2)); ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-file-invoice"></i>
                        Receivable
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card gradient-danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Unpaid Bills (AP)</div>
                    <div class="stat-value">$<?php echo e(number_format($payable ?? 0, 2)); ?></div>
                    <div class="stat-trend" style="color: #fecaca;">
                        <i class="fas fa-money-bill"></i>
                        Payable
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
</div>
        </div>
    </div>
</div>

<?php if(($unpaidInvoices ?? collect([]))->count() > 0 || ($unpaidBills ?? collect([]))->count() > 0): ?>
<div class="row g-4 mb-4">
    <?php if(($unpaidInvoices ?? collect([]))->count() > 0): ?>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
                <h6 class="mb-0 fw-semibold text-warning">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Unpaid Invoices (AR)
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Customer</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $unpaidInvoices->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($inv->customer->name); ?></td>
                            <td class="text-end">$<?php echo e(number_format($inv->balance, 2)); ?></td>
                            <td class="text-end <?php echo e($inv->due_date < now() ? 'text-danger' : ''); ?>"><?php echo e($inv->due_date->format('M d')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if(($unpaidBills ?? collect([]))->count() > 0): ?>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-danger bg-opacity-10 border-0 py-3">
                <h6 class="mb-0 fw-semibold text-danger">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Unpaid Bills (AP)
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Vendor</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $unpaidBills->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($bill->vendor ?? 'N/A'); ?></td>
                            <td class="text-end">$<?php echo e(number_format($bill->amount, 2)); ?></td>
                            <td class="text-end"><?php echo e($bill->date->format('M d')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Income vs Expenses (<?php echo e(now()->year); ?>)
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">Yearly Overview</span>
            </div>
            <div class="card-body p-4">
                <?php
                    $hasIncome = ($monthlyIncome ?? collect([]))->sum() > 0;
                    $hasExpense = ($monthlyExpense ?? collect([]))->sum() > 0;
                ?>
                
                <?php if($hasIncome || $hasExpense): ?>
                    <div style="height: 350px;">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135706.png" 
                                 alt="No Data" 
                                 style="width: 180px; height: auto; opacity: 0.85;"
                                 class="img-fluid">
                        </div>
                        <h5 class="text-muted fw-medium mb-2">No Data Available Yet</h5>
                        <p class="text-muted small mb-0">
                            Start adding incomes and expenses to see your financial overview here.
                        </p>
                        <div class="mt-3">
                            <a href="<?php echo e(route('incomes.create')); ?>" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-plus me-1"></i> Add Income
                            </a>
                            <a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Expense
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($recentTransactions->count() > 0): ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-clock-rotate-left me-2 text-primary"></i>
                    Recent Transactions
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="transaction-icon me-3">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?php echo e($entry->entry_number); ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 150px;">
                                            <?php echo e($entry->description ?? 'No description'); ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted"><?php echo e($entry->date->format('M d')); ?></div>
                                    <span class="badge bg-<?php echo e($entry->status === 'posted' ? 'success' : 'warning'); ?> bg-opacity-10 text-<?php echo e($entry->status === 'posted' ? 'success' : 'warning'); ?>">
                                        <?php echo e($entry->status); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <div>No transactions yet</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                    Pending Invoices
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">Invoice #</th>
                            <th class="border-0">Customer</th>
                            <th class="border-0">Date</th>
                            <th class="border-0">Due Date</th>
                            <th class="border-0 text-end">Amount</th>
                            <th class="border-0 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="align-middle">
                                <td class="px-4 fw-medium"><?php echo e($invoice->invoice_number); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            <?php echo e(strtoupper(substr($invoice->customer->name, 0, 1))); ?>

                                        </div>
                                        <?php echo e($invoice->customer->name); ?>

                                    </div>
                                </td>
                                <td><?php echo e($invoice->issue_date->format('M d, Y')); ?></td>
                                <td class="<?php echo e($invoice->status === 'overdue' ? 'text-danger' : ''); ?>">
                                    <?php echo e($invoice->due_date->format('M d, Y')); ?>

                                </td>
                                <td class="text-end fw-semibold">$<?php echo e(number_format($invoice->total, 2)); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($invoice->status === 'overdue' ? 'danger' : 'warning'); ?> bg-opacity-10 text-<?php echo e($invoice->status === 'overdue' ? 'danger' : 'warning'); ?>">
                                        <?php echo e(ucfirst($invoice->status)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                                    <div>No pending invoices</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.stat-card {
    border-radius: 16px;
    padding: 24px;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}
.gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.gradient-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
.stat-label { font-size: 0.85rem; opacity: 0.85; margin-bottom: 4px; font-weight: 500; }
.stat-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 8px; }
.stat-trend { font-size: 0.75rem; display: flex; align-items: center; gap: 4px; opacity: 0.9; }
.stat-trend.positive { color: #d1fae5; }
.stat-trend.negative { color: #fecaca; }
.stat-icon { width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.transaction-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-size: 0.875rem; }
.avatar-circle { width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 600; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('incomeExpenseChart');
if (ctx) {
    const chartCtx = ctx.getContext('2d');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const incomeData = Object.values(<?php echo e(json_encode($monthlyIncome)); ?>);
    const expenseData = Object.values(<?php echo e(json_encode($monthlyExpense)); ?>);

    const incomeGradient = chartCtx.createLinearGradient(0, 0, 0, 350);
    incomeGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    incomeGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    const expenseGradient = chartCtx.createLinearGradient(0, 0, 0, 350);
    expenseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.3)');
    expenseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

    new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Income',
                    data: incomeData,
                    borderColor: '#10b981',
                    backgroundColor: incomeGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Expenses',
                    data: expenseData,
                    borderColor: '#ef4444',
                    backgroundColor: expenseGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 25, font: { size: 13, weight: '500' } } },
                tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', titleFont: { size: 14 }, bodyFont: { size: 13 }, padding: 12, cornerRadius: 8 }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' }, ticks: { callback: function(value) { return '$' + value.toLocaleString(); } } },
                x: { grid: { display: false } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accounting-system\resources\views/dashboard.blade.php ENDPATH**/ ?>