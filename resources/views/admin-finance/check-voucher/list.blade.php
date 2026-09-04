<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Check Voucher List</h4>
                    <a href="{{ route('admin-finance.check-voucher.create') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-plus"></i>New Voucher
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>CV No.</th>
                                    <th>Date</th>
                                    <th>Reference (Check #)</th>
                                    <th>Payee/Memo</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td><strong>{{ $entry->entry_no }}</strong></td>
                                    <td>{{ date('M d, Y', strtotime($entry->date)) }}</td>
                                    <td>{{ $entry->reference ?? 'N/A' }}</td>
                                    <td>
                                        <div class="small text-muted">{{ $entry->memo }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $totalAmount = $entry->items->where('debit', '>', 0)->sum('debit');
                                        @endphp
                                        <strong>₱ {{ number_format($totalAmount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge light badge-success">
                                            <i class="fa fa-circle text-success me-1"></i>
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin-finance.check-voucher.show', $entry->id) }}" class="btn btn-primary shadow btn-xs sharp me-1" title="View Details"><i class="fas fa-eye"></i></a>
                                            <button type="button" class="btn btn-danger shadow btn-xs sharp" 
                                                data-toggle="modal"
                                                data-bs-toggle="modal" 
                                                data-target="#deleteModal"
                                                data-bs-target="#deleteModal" 
                                                data-url="{{ route('accounting.journal.destroy', $entry->id) }}"
                                                data-entry-no="{{ $entry->entry_no }}"
                                                title="Delete Voucher">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No check vouchers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete Check Voucher <strong id="modalEntryNo"></strong>? This action will reverse the associated accounting entries and cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete Voucher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                const handleModalShow = function(event) {
                    const button = event.relatedTarget;
                    const url = button.getAttribute('data-url');
                    const entryNo = button.getAttribute('data-entry-no');
                    const form = deleteModal.querySelector('#deleteForm');
                    const entrySpan = deleteModal.querySelector('#modalEntryNo');
                    
                    form.setAttribute('action', url);
                    entrySpan.textContent = entryNo;
                };

                deleteModal.addEventListener('show.bs.modal', handleModalShow);
                if (window.jQuery) {
                    $('#deleteModal').on('show.bs.modal', handleModalShow);
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
