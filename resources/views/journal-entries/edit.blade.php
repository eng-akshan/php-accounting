@extends('layouts.master')

@section('title', 'Edit Journal Entry')
@section('page-title', 'Edit Journal Entry')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Edit Journal Entry</span>
        <div id="balanceStatus">
            <span class="badge bg-warning">Calculating...</span>
        </div>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div id="balanceAlert" class="alert alert-warning d-none">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="balanceMessage">Journal entry is not balanced. Debits must equal Credits.</span>
        </div>

        <form method="POST" action="{{ route('journal-entries.update', $journalEntry) }}" id="entryForm">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $journalEntry->date->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" {{ $journalEntry->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ $journalEntry->status === 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="voided" {{ $journalEntry->status === 'voided' ? 'selected' : '' }}>Voided</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ $journalEntry->description }}" required>
                </div>
            </div>

            <h5>Journal Lines</h5>
            <table class="table" id="linesTable">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Description</th>
                        <th style="width: 120px;">Debit</th>
                        <th style="width: 120px;">Credit</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="linesBody">
                    @foreach($journalEntry->lines as $index => $line)
                        <tr>
                            <td>
                                <select name="lines[{{ $index }}][account_id]" class="form-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $line->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_number }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[{{ $index }}][description]" class="form-control" value="{{ $line->description }}"></td>
                            <td><input type="number" name="lines[{{ $index }}][debit]" class="form-control text-end debit" step="0.01" min="0" value="{{ $line->debit }}"></td>
                            <td><input type="number" name="lines[{{ $index }}][credit]" class="form-control text-end credit" step="0.01" min="0" value="{{ $line->credit }}"></td>
                            <td>
                                @if($loop->index > 1)
                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); window.calcTotals&&window.calcTotals()"><i class="fas fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="2"><strong>Totals</strong></td>
                        <td class="text-end"><strong id="totalDebit">$0.00</strong></td>
                        <td class="text-end"><strong id="totalCredit">$0.00</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-secondary btn-sm" onclick="addLine()">
                    <i class="fas fa-plus"></i> Add Line
                </button>
                <button type="button" class="btn btn-info btn-sm" id="autoBalanceBtn" onclick="autoBalance()">
                    <i class="fas fa-balance-scale"></i> Auto Balance
                </button>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">Update Entry</button>
                <a href="{{ route('journal-entries.show', $journalEntry) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var lineCount = {{ $journalEntry->lines->count() }};
    var isBalanced = false;
    var totalDebitEl = document.getElementById('totalDebit');
    var totalCreditEl = document.getElementById('totalCredit');
    var balanceAlert = document.getElementById('balanceAlert');
    var balanceMessage = document.getElementById('balanceMessage');
    var submitBtn = document.getElementById('submitBtn');
    var balanceStatus = document.getElementById('balanceStatus');
    var linesBody = document.querySelector('#linesTable tbody');
    var entryForm = document.getElementById('entryForm');

    function calculateTotals() {
        var debitInputs = linesBody.querySelectorAll('.debit');
        var creditInputs = linesBody.querySelectorAll('.credit');
        
        var totalDebit = 0;
        var totalCredit = 0;
        
        debitInputs.forEach(function(input) {
            var val = parseFloat(input.value) || 0;
            totalDebit += val;
        });
        
        creditInputs.forEach(function(input) {
            var val = parseFloat(input.value) || 0;
            totalCredit += val;
        });

        totalDebitEl.textContent = '$' + totalDebit.toFixed(2);
        totalCreditEl.textContent = '$' + totalCredit.toFixed(2);

        var diff = Math.abs(totalDebit - totalCredit);
        isBalanced = diff < 0.01 && totalDebit > 0;

        if (totalDebit > 0 || totalCredit > 0) {
            if (isBalanced) {
                balanceAlert.classList.add('d-none');
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-primary');
                balanceStatus.innerHTML = '<span class="badge bg-success"><i class="fas fa-check"></i> Balanced</span>';
            } else {
                balanceAlert.classList.remove('d-none');
                var diffStr = diff.toFixed(2);
                balanceMessage.textContent = 'Journal entry is not balanced. Difference: $' + diffStr;
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-secondary');
                balanceStatus.innerHTML = '<span class="badge bg-danger">Unbalanced: $' + diffStr + '</span>';
            }
        } else {
            balanceAlert.classList.add('d-none');
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
            balanceStatus.innerHTML = '<span class="badge bg-secondary">Enter amounts</span>';
        }
    }

    window.autoBalance = function() {
        var debitInputs = linesBody.querySelectorAll('.debit');
        var creditInputs = linesBody.querySelectorAll('.credit');
        
        var totalDebit = 0;
        var totalCredit = 0;
        
        debitInputs.forEach(function(input) {
            totalDebit += parseFloat(input.value) || 0;
        });
        
        creditInputs.forEach(function(input) {
            totalCredit += parseFloat(input.value) || 0;
        });

        var rows = linesBody.querySelectorAll('tr');
        if (rows.length === 0) return;

        var diff = totalDebit - totalCredit;

        if (Math.abs(diff) > 0.01) {
            var lastRow = rows[rows.length - 1];
            var lastDebit = lastRow.querySelector('.debit');
            var lastCredit = lastRow.querySelector('.credit');

            if (totalDebit > totalCredit) {
                var newCredit = parseFloat(lastCredit.value || 0) + Math.abs(diff);
                lastCredit.value = newCredit.toFixed(2);
            } else {
                var newDebit = parseFloat(lastDebit.value || 0) + Math.abs(diff);
                lastDebit.value = newDebit.toFixed(2);
            }

            calculateTotals();
        }
    };

    window.addLine = function() {
        var accounts = @json($accounts);
        var options = '<option value="">Select Account</option>';
        accounts.forEach(function(a) {
            options += '<option value="' + a.id + '">' + a.account_number + ' - ' + a.name + '</option>';
        });

        var html = 
            '<tr>' +
                '<td><select name="lines[' + lineCount + '][account_id]" class="form-select" required>' + options + '</select></td>' +
                '<td><input type="text" name="lines[' + lineCount + '][description]" class="form-control"></td>' +
                '<td><input type="number" name="lines[' + lineCount + '][debit]" class="form-control text-end debit" step="0.01" min="0" value="0"></td>' +
                '<td><input type="number" name="lines[' + lineCount + '][credit]" class="form-control text-end credit" step="0.01" min="0" value="0"></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest(\'tr\').remove(); window.calcTotals&&window.calcTotals()"><i class="fas fa-times"></i></button></td>' +
            '</tr>';
        linesBody.insertAdjacentHTML('beforeend', html);
        lineCount++;

        var newDebitInputs = linesBody.querySelectorAll('.debit');
        var newCreditInputs = linesBody.querySelectorAll('.credit');
        
        newDebitInputs.forEach(function(input) {
            input.oninput = calculateTotals;
        });
        newCreditInputs.forEach(function(input) {
            input.oninput = calculateTotals;
        });
        
        window.calcTotals = calculateTotals;
        calculateTotals();
    };

    window.calcTotals = calculateTotals;

    var debitInputs = linesBody.querySelectorAll('.debit');
    var creditInputs = linesBody.querySelectorAll('.credit');
    
    debitInputs.forEach(function(input) {
        input.oninput = calculateTotals;
    });
    creditInputs.forEach(function(input) {
        input.oninput = calculateTotals;
    });

    calculateTotals();

    entryForm.onsubmit = function(e) {
        if (!isBalanced) {
            e.preventDefault();
            alert('Journal entry must be balanced (Total Debit = Total Credit) before saving.');
            return false;
        }
    };
})();
</script>
@endsection