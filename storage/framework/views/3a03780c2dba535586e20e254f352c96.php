<?php $__env->startSection('title', 'Invoices'); ?>
<?php $__env->startSection('page-title', 'Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Invoices</span>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Invoice
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('invoices.index')); ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>Sent</option>
                        <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><a href="<?php echo e(route('invoices.show', $invoice)); ?>"><?php echo e($invoice->invoice_number); ?></a></td>
                        <td><?php echo e($invoice->customer->name); ?></td>
                        <td><?php echo e($invoice->issue_date->format('M d, Y')); ?></td>
                        <td><?php echo e($invoice->due_date->format('M d, Y')); ?></td>
                        <td class="text-end">$<?php echo e(number_format($invoice->total, 2)); ?></td>
                        <td class="text-end">$<?php echo e(number_format($invoice->paid_amount, 2)); ?></td>
                        <td><span class="badge bg-<?php echo e($invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'info' : ($invoice->status === 'cancelled' ? 'danger' : 'warning'))); ?>"><?php echo e(ucfirst($invoice->status)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php echo e($invoices->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\accounting-system\resources\views/invoices/index.blade.php ENDPATH**/ ?>