<x-app-layout :title="'Journal Entry Details'" :sidebar="'admin-finance'">
    <div class="row g-3">
        <!-- Main Entry Details Card -->
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 h-auto" style="border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff;">
                <div class="card-header border-0 bg-white pt-3 pb-1 px-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0 fw-bold fs-18" style="color: #000000;">Entry #{{ $entry->entry_no }}</h4>
                        <p class="text-muted small mb-0 mt-1">Posted on {{ date('M d, Y', strtotime($entry->date)) }}</p>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-sm text-white fw-bold px-3 d-flex align-items-center justify-content-center" style="background-color: #D9251C; border-color: #D9251C; height: 32px; border-radius: 4px; font-size: 0.82rem;">
                            <i class="las la-print me-1"></i>Print
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-3 pt-2">
                    <!-- Metadata Header Grid -->
                    <div class="row mb-3 g-2">
                        <div class="col-sm-4">
                            <div class="p-2 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px; margin-bottom: 2px; color: #000000;">Reference</span>
                                <span style="font-size: 0.85rem; color: #475569; font-weight: 500;">{{ $entry->reference ?? 'None' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px; margin-bottom: 2px; color: #000000;">Entry Type</span>
                                <span style="font-size: 0.85rem; color: #475569; font-weight: 500;">{{ $entry->entry_type ?? 'GJE' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px; margin-bottom: 2px; color: #000000;">Posting Status</span>
                                <div>
                                    @if($entry->status === 'posted')
                                        <span class="status-badge-posted">Posted</span>
                                    @else
                                        <span class="status-badge-draft">Draft</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- General Memo Callout -->
                    <div class="mb-3 p-2 border-start border-3" style="border-color: #D9251C !important; background-color: #f8fafc; border-radius: 0 4px 4px 0;">
                        <span class="d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px; margin-bottom: 2px; color: #000000;">General Memo</span>
                        <span style="font-size: 0.85rem; color: #475569; font-weight: 500;">{{ $entry->memo ?? 'No description provided.' }}</span>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive" style="border: none;">
                        <table class="table journal-table align-middle" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-end" style="width: 150px;">Debit</th>
                                    <th class="text-end" style="width: 150px;">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entry->items as $item)
                                <tr>
                                    <td>
                                        <span style="color: #334155; font-weight: 600;">{{ $item->account->code }} · {{ $item->account->name }}</span>
                                        @if($item->memo)
                                            <div class="small text-muted mt-1 ps-2 border-start border-1" style="font-size: 0.78rem; border-color: #cbd5e1 !important;">{{ $item->memo }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end" style="color: #475569; font-weight: 500;">
                                        {{ $item->debit > 0 ? number_format($item->debit, 2) : '—' }}
                                    </td>
                                    <td class="text-end" style="color: #475569; font-weight: 500;">
                                        {{ $item->credit > 0 ? number_format($item->credit, 2) : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td class="text-end fw-bold" style="color: #000000;">Totals</td>
                                    <td class="text-end fw-bold" style="padding-right: 8px; color: #334155;">₱{{ number_format($entry->items->sum('debit'), 2) }}</td>
                                    <td class="text-end fw-bold" style="padding-right: 8px; color: #334155;">₱{{ number_format($entry->items->sum('credit'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="card-footer d-flex justify-content-between align-items-center border-top p-3" style="background-color: #ffffff;">
                    <div>
                        <a href="{{ route('accounting.journal.index') }}" class="btn d-inline-flex align-items-center justify-content-center fw-bold" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; height: 36px; padding: 0 1.25rem; border-radius: 4px; font-size: 0.85rem; text-decoration: none;">
                            Back to List
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger shadow btn-sm fw-bold px-3 d-inline-flex align-items-center justify-content-center" style="height: 36px; border-radius: 4px; font-size: 0.85rem;" 
                            data-toggle="modal" 
                            data-bs-toggle="modal" 
                            data-target="#deleteModal" 
                            data-bs-target="#deleteModal">
                            Delete Entry
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Audit Card -->
        <div class="col-xl-4">
             <div class="card shadow-sm border-0 h-auto" style="border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff;">
                <div class="card-header border-0 bg-white pt-3 pb-1 px-3">
                    <h4 class="card-title mb-0 fw-bold fs-16" style="color: #000000;">Audit Details</h4>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                            <span class="fw-bold" style="color: #000000;">Post ID</span>
                            <span style="color: #475569; font-weight: 500;">#{{ $entry->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                            <span class="fw-bold" style="color: #000000;">Currency</span>
                            <span style="color: #475569; font-weight: 500;">{{ $entry->currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                            <span class="fw-bold" style="color: #000000;">Exchange Rate</span>
                            <span style="color: #475569; font-weight: 500;">{{ number_format($entry->exchange_rate, 4) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2" style="border-color: #f1f5f9 !important;">
                            <span class="fw-bold" style="color: #000000;">Created By</span>
                            <span style="color: #475569; font-weight: 500;">{{ $entry->creator->name ?? 'System' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold" style="color: #000000;">Date Created</span>
                            <span style="color: #475569; font-weight: 500;">{{ $entry->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 8px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    Are you sure you want to delete journal entry <strong style="color: #D9251C;">{{ $entry->entry_no }}</strong>? This action will reverse all associated ledger postings and cannot be undone.
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal" data-dismiss="modal" style="border-radius: 4px;">Cancel</button>
                    <form action="{{ route('accounting.journal.destroy', $entry->id) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn text-white btn-sm px-3" style="background-color: #D9251C; border-color: #D9251C; border-radius: 4px;">Yes, Delete Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
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
            padding: 8px 8px;
            font-size: 0.85rem;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
        }

        .total-row td {
            background-color: #f8fafc;
            padding: 10px 8px;
            border-bottom: 2px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            font-size: 0.85rem;
        }

        .status-badge-posted {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-weight: 600;
            font-size: 0.72rem;
            padding: 2px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-badge-draft {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            font-weight: 600;
            font-size: 0.72rem;
            padding: 2px 10px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
    @endpush
</x-app-layout>
