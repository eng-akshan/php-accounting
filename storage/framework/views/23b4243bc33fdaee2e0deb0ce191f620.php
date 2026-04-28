<?php $__env->startSection('title', 'Expenses'); ?>
<?php $__env->startSection('page-title', 'Expenses'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Expenses</span>
        <a href="<?php echo e(route('expenses.create')); ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Expense
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('expenses.index')); ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        <option value="salary" <?php echo e(request('category') == 'salary' ? 'selected' : ''); ?>>Salary</option>
                        <option value="rent" <?php echo e(request('category') == 'rent' ? 'selected' : ''); ?>>Rent</option>
                        <option value="utilities" <?php echo e(request('category') == 'utilities' ? 'selected' : ''); ?>>Utilities</option>
                        <option value="supplies" <?php echo e(request('category') == 'supplies' ? 'selected' : ''); ?>>Supplies</option>
                        <option value="other" <?php echo e(request('category') == 'other' ? 'selected' : ''); ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select name="payment_status" class="form-select">
                        <option value="">All</option>
                        <option value="unpaid" <?php echo e(request('payment_status') == 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                        <option value="paid" <?php echo e(request('payment_status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><a href="<?php echo e(route('expenses.show', $expense)); ?>"><?php echo e($expense->reference_number); ?></a></td>
                        <td><?php echo e($expense->description); ?></td>
                        <td class="text-end">$<?php echo e(number_format($expense->amount, 2)); ?></td>
                        <td><?php echo e($expense->date->format('M d, Y')); ?></td>
                        <td><span class="badge bg-secondary"><?php echo e(ucfirst($expense->category)); ?></span></td>
                        <td>
                            <?php if($expense->payment_status === 'paid'): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($expense->payment_status !== 'paid'): ?>
                                <form action="<?php echo e(route('expenses.pay-bill', $expense)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success">Pay</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">No expenses found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php echo e($expenses->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accounting-system\resources\views/expenses/index.blade.php ENDPATH**/ ?>