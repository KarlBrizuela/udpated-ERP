<x-app-layout :title="'Make General Journal Entries'" :sidebar="'admin-finance'">
@php
    $assets = $accounts->where('type', 'Asset')->where('category', '!=', 'Cash & Bank');
    $bankAccounts = $accounts->where('type', 'Asset')->where('category', 'Cash & Bank');
    $liabilities = $accounts->where('type', 'Liability');
    $income = $accounts->where('type', 'Income');
    $expenses = $accounts->where('type', 'Expense');
    $equity = $accounts->where('type', 'Equity');
@endphp
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow-sm border-0" style="border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff;">
                <div class="card-header border-0 bg-white pt-3 pb-1 px-3">
                    <h4 class="card-title mb-0 fw-bold fs-18" style="color: #000000;">Make General Journal Entries</h4>
                    <p class="text-muted small mb-0 mt-1">Record manual debit and credit transactions to the general ledger.</p>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('accounting.journal.store') }}" method="POST" id="journalEntryForm">
                        @csrf
                        
                        <div class="journal-header mb-3">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">CURRENCY</label>
                                        <select class="form-control form-select-sm" name="currency" id="journalCurrency" style="height: 36px; border-color: #cbd5e1; font-size: 0.85rem; border-radius: 4px;">
                                            <option value="PHP" {{ old('currency', 'PHP') == 'PHP' ? 'selected' : '' }}>Philippine peso</option>
                                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>US Dollar</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">DATE</label>
                                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required style="height: 36px; border-color: #cbd5e1; font-size: 0.85rem; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">ENTRY NO.</label>
                                        <input type="text" name="entry_no" class="form-control" value="{{ $entryNo }}" required style="height: 36px; border-color: #cbd5e1; font-size: 0.85rem; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">REFERENCE NO.</label>
                                        <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="Ref / Cheque / Doc No." style="height: 36px; border-color: #cbd5e1; font-size: 0.85rem; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group mb-2">
                                        <label class="form-label">EXCHANGE RATE</label>
                                        <input type="number" step="0.0001" name="exchange_rate" id="journalExchangeRate" class="form-control" value="{{ old('exchange_rate', '1.0000') }}" readonly style="height: 36px; border-color: #cbd5e1; font-size: 0.85rem; background-color: #f1f5f9; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive journal-table-wrapper" style="border: none;">
                            <table class="table journal-table table-align-middle" id="journalItemsTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th style="width: 320px;">ACCOUNT</th>
                                        <th style="width: 150px; text-align: right;">DEBIT</th>
                                        <th style="width: 150px; text-align: right;">CREDIT</th>
                                        <th>MEMO</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="journalItemsBody">
                                    @for($i = 0; $i < 2; $i++)
                                    <tr class="journal-row">
                                        <td>
                                            <select name="items[{{ $i }}][account_id]" class="form-control select2-account">
                                                <option value="">Select Account</option>
                                                @if($assets->isNotEmpty())
                                                    <optgroup label="ASSETS">
                                                        @foreach($assets as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if($bankAccounts->isNotEmpty())
                                                    <optgroup label="BANK ACCOUNTS">
                                                        @foreach($bankAccounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if($liabilities->isNotEmpty())
                                                    <optgroup label="LIABILITIES">
                                                        @foreach($liabilities as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if($income->isNotEmpty())
                                                    <optgroup label="INCOME">
                                                        @foreach($income as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if($expenses->isNotEmpty())
                                                    <optgroup label="EXPENSES">
                                                        @foreach($expenses as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if($equity->isNotEmpty())
                                                    <optgroup label="EQUITY">
                                                        @foreach($equity as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][debit]" class="form-control debit-input text-end" step="0.01" min="0" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $i }}][credit]" class="form-control credit-input text-end" step="0.01" min="0" placeholder="0.00">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $i }}][memo]" class="form-control" placeholder="Memo">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="remove-row d-inline-flex align-items-center justify-content-center" style="background-color: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.15); color: #ef4444; width: 28px; height: 28px; border-radius: 4px; transition: all 0.15s ease-in-out; padding: 0;" title="Remove row">
                                                <i class="las la-trash fs-14"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td class="text-end fw-bold" style="color: #475569;">Totals</td>
                                        <td class="text-end fw-bold" id="totalDebit" style="color: #ef4444; padding-right: 12px;">0.00</td>
                                        <td class="text-end fw-bold" id="totalCredit" style="color: #ef4444; padding-right: 12px;">0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-2">
                            <button type="button" id="addRowBtn" class="btn btn-sm text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 32px; border-radius: 4px; font-size: 0.82rem;">
                                <i class="las la-plus me-1"></i>Add Line
                            </button>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">GENERAL MEMO</label>
                                    <textarea name="memo" class="form-control" rows="2" placeholder="Enter transaction memo..." style="border-color: #cbd5e1; font-size: 0.85rem; border-radius: 4px;"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                            <div>
                                <a href="{{ route('accounting.journal.index') }}" class="btn d-inline-flex align-items-center justify-content-center fw-bold" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; height: 36px; padding: 0 1.25rem; border-radius: 4px; font-size: 0.85rem; text-decoration: none;">
                                    Back to List
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn d-inline-flex align-items-center justify-content-center fw-bold" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; height: 36px; padding: 0 1.25rem; border-radius: 4px; font-size: 0.85rem;">
                                    Clear Form
                                </button>
                                <button type="submit" class="btn text-white fw-bold px-4 d-inline-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 36px; padding: 0 1.5rem; border-radius: 4px; font-size: 0.85rem;">
                                    Save & Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.68rem;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .journal-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.8px;
            padding: 10px 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .journal-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .journal-table .form-control {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 4px 8px;
            font-size: 0.82rem;
            border-radius: 4px;
            height: 32px;
            transition: all 0.15s ease-in-out;
        }

        .journal-table .form-control:focus {
            background-color: #ffffff;
            border-color: #D9251C;
            box-shadow: 0 0 0 2px rgba(217, 37, 28, 0.1);
        }

        .total-row td {
            background-color: #f8fafc;
            padding: 10px 8px;
            border-bottom: 2px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85rem;
        }

        /* Select2 Theme Customizations */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            height: 32px !important;
            border-radius: 4px !important;
            display: flex;
            align-items: center;
            transition: all 0.15s ease-in-out;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            background-color: #ffffff !important;
            border-color: #D9251C !important;
            box-shadow: 0 0 0 2px rgba(217, 37, 28, 0.1) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 8px !important;
            font-size: 0.82rem !important;
            color: #0f172a !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }

        /* Subtler placeholders for all input fields */
        ::placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }
        :-ms-input-placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }
        ::-ms-input-placeholder {
            color: #94a3b8 !important;
            opacity: 0.7 !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        jQuery(document).ready(function($) {
            // Use DOM count to be safer than PHP variable
            let rowIdx = $('.journal-row').length;

            // Notification fallback helper
            function showNotification(message, type = 'error') {
                if (window.toastr) {
                    toastr[type](message);
                } else {
                    alert(message);
                }
            }

            function initSelect2(element) {
                try {
                    const target = element ? $(element).find('.select2-account') : $('.select2-account');
                    if (target.length && typeof target.select2 === 'function') {
                        target.each(function() {
                            if (!$(this).hasClass("select2-hidden-accessible")) {
                                $(this).select2({
                                    placeholder: "Select Account",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }
                        });
                    }
                } catch (e) {
                    console.warn('Select2 initialization failed:', e);
                }
            }

            function calculateTotals() {
                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit-input').each(function() {
                    totalDebit += parseFloat($(this).val()) || 0;
                });

                $('.credit-input').each(function() {
                    totalCredit += parseFloat($(this).val()) || 0;
                });

                $('#totalDebit').text(totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#totalCredit').text(totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2}));

                if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
                    $('#totalDebit, #totalCredit').css('color', '#16803d');
                } else {
                    $('#totalDebit, #totalCredit').css('color', '#D9251C');
                }
            }

            // Remove Row Listener
            $(document).on('click', '.remove-row', function() {
                if ($('.journal-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                } else {
                    showNotification('A journal entry must contain at least 1 transaction line.', 'error');
                }
            });

            // Add Row Listener
            $('#addRowBtn').on('click', function() {
                const newRow = `
                    <tr class="journal-row">
                        <td>
                            <select name="items[${rowIdx}][account_id]" class="form-control select2-account">
                                <option value="">Select Account</option>
                                @if($assets->isNotEmpty())
                                    <optgroup label="ASSETS">
                                        @foreach($assets as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($bankAccounts->isNotEmpty())
                                    <optgroup label="BANK ACCOUNTS">
                                        @foreach($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($liabilities->isNotEmpty())
                                    <optgroup label="LIABILITIES">
                                        @foreach($liabilities as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($income->isNotEmpty())
                                    <optgroup label="INCOME">
                                        @foreach($income as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($expenses->isNotEmpty())
                                    <optgroup label="EXPENSES">
                                        @foreach($expenses as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($equity->isNotEmpty())
                                    <optgroup label="EQUITY">
                                        @foreach($equity as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${rowIdx}][debit]" class="form-control debit-input text-end" step="0.01" min="0" placeholder="0.00">
                        </td>
                        <td>
                            <input type="number" name="items[${rowIdx}][credit]" class="form-control credit-input text-end" step="0.01" min="0" placeholder="0.00">
                        </td>
                        <td>
                            <input type="text" name="items[${rowIdx}][memo]" class="form-control" placeholder="Memo">
                        </td>
                        <td class="text-center">
                            <button type="button" class="remove-row d-inline-flex align-items-center justify-content-center" style="background-color: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.15); color: #ef4444; width: 28px; height: 28px; border-radius: 4px; transition: all 0.15s ease-in-out; padding: 0;" title="Remove row">
                                <i class="las la-trash fs-14"></i>
                            </button>
                        </td>
                    </tr>
                `;
                const $newRow = $(newRow);
                $('#journalItemsBody').append($newRow);
                initSelect2($newRow);
                rowIdx++;
            });

            // Calculate totals dynamically on inputs
            $(document).on('input', '.debit-input, .credit-input', function() {
                calculateTotals();
            });

            // Form Submit validation
            $('#journalEntryForm').on('submit', function(e) {
                const totalDebit = parseFloat($('#totalDebit').text().replace(/,/g, '')) || 0;
                const totalCredit = parseFloat($('#totalCredit').text().replace(/,/g, '')) || 0;

                if (Math.abs(totalDebit - totalCredit) > 0.01) {
                    e.preventDefault();
                    showNotification('Journal entry must balance! (Difference: ' + (totalDebit - totalCredit).toFixed(2) + ')', 'error');
                } else if (totalDebit <= 0) {
                    e.preventDefault();
                    showNotification('Total amount must be greater than zero.', 'error');
                }
            });

            // Currency exchange rate toggler
            $('#journalCurrency').on('change', function() {
                if ($(this).val() === 'USD') {
                    $('#journalExchangeRate').prop('readonly', false).css('background-color', '#ffffff');
                } else {
                    $('#journalExchangeRate').val('1.0000').prop('readonly', true).css('background-color', '#f1f5f9');
                }
            });

            // Initial Calculations and Select2 calls
            initSelect2();
            calculateTotals();
        });
    </script>
    @endpush
</x-app-layout>
