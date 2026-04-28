<?php $__env->startSection('title', 'Profit & Loss Report'); ?>
<?php $__env->startSection('page-title', 'Profit & Loss Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Profit & Loss Statement</span>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo e($dateFrom); ?>">
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e($dateTo); ?>">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="text-white-50">Total Revenue</div>
                    <div class="value">$<?php echo e(number_format($totalRevenue, 2)); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card danger">
                    <div class="text-white-50">Total Expenses</div>
                    <div class="value">$<?php echo e(number_format($totalExpenses, 2)); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card <?php echo e($netProfit >= 0 ? 'info' : 'danger'); ?>">
                    <div class="text-white-50">Net <?php echo e($netProfit >= 0 ? 'Profit' : 'Loss'); ?></div>
                    <div class="value">$<?php echo e(number_format(abs($netProfit), 2)); ?></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Revenue</h5>
                <table class="table table-sm">
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $revenue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item['account']->name); ?></td>
                                <td class="text-end">$<?php echo e(number_format($item['total'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="2" class="text-muted">No revenue</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-success">
                            <th>Total Revenue</th>
                            <th class="text-end">$<?php echo e(number_format($totalRevenue, 2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Expenses</h5>
                <table class="table table-sm">
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item['account']->name); ?></td>
                                <td class="text-end">$<?php echo e(number_format($item['total'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="2" class="text-muted">No expenses</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-danger">
                            <th>Total Expenses</th>
                            <th class="text-end">$<?php echo e(number_format($totalExpenses, 2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accounting-system\resources\views/reports/profit-loss.blade.php ENDPATH**/ ?>