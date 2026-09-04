<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>New Summary Compilation</h4>
                    <p class="mb-0">Group items for Accounting review</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin-finance.credit-collection.jv-requests.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Line Items Grid (Full Width) -->
                <div class="col-xl-12">
                    <div class="card h-auto">
                        <div class="card-header">
                            <h5 class="card-title">Item List for Verification</h5>
                            <button type="button" class="btn btn-primary btn-xxs shadow" onclick="addNewJvRow()">
                                <i class="las la-plus"></i> Add Row
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm vertical-middle" id="jv_items_table">
                                    <thead class="bg-light">
                                        <tr class="text-dark fw-bold">
                                            <th style="width: 140px">Type</th>
                                            <th style="width: 130px">Ref #</th>
                                            <th>Entity Name</th>
                                            <th style="width: 130px">Amount (₱)</th>
                                            <th style="width: 120px">Remarks</th>
                                            <th style="width: 40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="jv_items_body">
                                        <!-- Initial Row -->
                                        <tr>
                                            <td>
                                                <select name="items[0][type]" class="form-control form-control-sm">
                                                    <option value="JV">JV Table</option>
                                                    <option value="SOA">Account Statement</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="items[0][reference_no]" class="form-control form-control-sm" placeholder="F2026-015"></td>
                                            <td>
                                                <input list="customer_list" name="items[0][customer_name]" class="form-control form-control-sm" placeholder="Search or Enter Customer" required>
                                                <datalist id="customer_list">
                                                    @foreach($customers as $c)
                                                        <option value="{{ $c->name }}">
                                                    @endforeach
                                                </datalist>
                                            </td>
                                            <td><input type="number" step="0.01" name="items[0][amount]" class="form-control form-control-sm text-end jv-amount" oninput="calculateTotalJv()" required></td>
                                            <td><input type="text" name="items[0][remarks]" class="form-control form-control-sm" value="QB Entry"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold fs-15">
                                            <td colspan="3" class="text-end">Total Amount: </td>
                                            <td class="text-end text-primary" id="jv_total_display">₱ 0.00</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary px-4 shadow">Submit for Accounting Verification</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let rowIdx = 1;
        function addNewJvRow() {
            const tbody = document.getElementById('jv_items_body');
            const row = `
                <tr id="row-${rowIdx}">
                    <td>
                        <select name="items[${rowIdx}][type]" class="form-control form-control-sm">
                            <option value="JV">JV Table</option>
                            <option value="SOA">Account Statement</option>
                        </select>
                    </td>
                    <td><input type="text" name="items[${rowIdx}][reference_no]" class="form-control form-control-sm"></td>
                    <td><input list="customer_list" name="items[${rowIdx}][customer_name]" class="form-control form-control-sm" required></td>
                    <td><input type="number" step="0.01" name="items[${rowIdx}][amount]" class="form-control form-control-sm text-end jv-amount" oninput="calculateTotalJv()" required></td>
                    <td><input type="text" name="items[${rowIdx}][remarks]" class="form-control form-control-sm" value="QB Entry"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-xxs shadow" onclick="removeJvRow(${rowIdx})">
                            <i class="las la-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
            rowIdx++;
        }

        function removeJvRow(idx) {
            document.getElementById(`row-${idx}`).remove();
            calculateTotalJv();
        }

        function calculateTotalJv() {
            let total = 0;
            document.querySelectorAll('.jv-amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('jv_total_display').innerText = '₱ ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        }
    </script>
</x-app-layout>
