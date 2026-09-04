<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Petty Cash Vouchers</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin-finance.petty-cash.summary') }}" class="btn btn-primary rounded shadow-sm px-4 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-file-alt me-1"></i>Monthly Summary
                        </a>
                        <a href="{{ route('admin-finance.petty-cash.create') }}" class="btn btn-primary rounded shadow-sm px-4 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-plus me-1"></i>New PCV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>PCV NO.</strong></th>
                                    <th><strong>TYPE</strong></th>
                                    <th><strong>DATE</strong></th>
                                    <th><strong>PAY TO</strong></th>
                                    <th><strong>ITEMS</strong></th>
                                    <th><strong>TOTAL AMOUNT</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vouchers as $voucher)
                                <tr>
                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                    <td>
                                        <span class="badge light {{ $voucher->type === 'freight' ? 'badge-info' : 'badge-secondary' }}">
                                            {{ ucfirst($voucher->type ?? 'fund') }}
                                        </span>
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                    <td>{{ $voucher->pay_to }}</td>
                                    <td><span class="badge light badge-primary">{{ $voucher->items_count ?? 0 }} item(s)</span></td>
                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $statusClass = 'badge-warning';
                                            if ($voucher->status === 'ongoing') $statusClass = 'badge-info';
                                            elseif ($voucher->status === 'completed' || $voucher->status === 'liquidated') $statusClass = 'badge-success';
                                            elseif ($voucher->status === 'rejected') $statusClass = 'badge-danger';
                                            elseif ($voucher->status === 'pending') $statusClass = 'badge-warning';
                                        @endphp
                                        <span class="badge light {{ $statusClass }}">
                                            {{ ucfirst($voucher->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin-finance.petty-cash.show', $voucher->id) }}" class="btn btn-primary shadow btn-xs sharp me-1" title="View"><i class="las la-eye"></i></a>
                                            <button type="button" class="btn btn-danger shadow btn-xs sharp"
                                                title="Delete"
                                                onclick="confirmDelete({{ $voucher->id }}, '{{ addslashes($voucher->pcv_number) }}')">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4"><i class="las la-file-invoice la-2x d-block mb-2"></i>No petty cash vouchers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $vouchers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:42px;height:42px;background:#fff0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="las la-trash text-danger" style="font-size:1.3rem;"></i>
                        </div>
                        <h5 class="modal-title mb-0 fw-bold" id="deleteModalLabel">Delete Voucher</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-1 text-muted">You are about to delete:</p>
                    <p class="fw-bold text-dark mb-3" id="deleteVoucherLabel">—</p>
                    <p class="small text-danger"><i class="las la-exclamation-triangle me-1"></i>This action cannot be undone. All items in this voucher will be permanently removed.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded px-4" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded px-4">Delete Voucher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id, pcvNumber) {
            document.getElementById('deleteVoucherLabel').textContent = 'PCV No. ' + pcvNumber;
            document.getElementById('deleteForm').action = '/admin-finance/petty-cash/' + id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>
