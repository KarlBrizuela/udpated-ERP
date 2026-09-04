<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Cashier Petty Cash Approvals</h4>
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

                    {{-- Top-Level Type Tabs --}}
                    <ul class="nav nav-tabs mb-4" id="cashierTypeTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="funds-type-tab" data-bs-toggle="tab" data-bs-target="#funds-type" type="button" role="tab">
                                <i class="las la-money-bill-wave me-1"></i> Petty Cash Funds
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="freight-type-tab" data-bs-toggle="tab" data-bs-target="#freight-type" type="button" role="tab">
                                <i class="las la-truck me-1"></i> Petty Cash Freight
                            </button>
                        </li>
                    </ul>

                    {{-- Top-Level Tab Content --}}
                    <div class="tab-content" id="cashierTypeTabsContent">
                        {{-- Petty Cash Funds Tab --}}
                        <div class="tab-pane fade show active" id="funds-type" role="tabpanel">
                            {{-- Nav Tabs --}}
                            <ul class="nav nav-tabs mb-4" id="cashierFundsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="funds-pending-tab" data-bs-toggle="tab" data-bs-target="#funds-pending" type="button" role="tab">
                                        Pending Approval 
                                        <span class="badge bg-warning ms-1 text-white">{{ $vouchers->filter(fn($v) => ($v->type ?? 'fund') === 'fund' && $v->status === 'pending')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="funds-ongoing-tab" data-bs-toggle="tab" data-bs-target="#funds-ongoing" type="button" role="tab">
                                        Ongoing 
                                        <span class="badge bg-info ms-1 text-white">{{ $vouchers->filter(fn($v) => ($v->type ?? 'fund') === 'fund' && $v->status === 'ongoing')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="funds-completed-tab" data-bs-toggle="tab" data-bs-target="#funds-completed" type="button" role="tab">
                                        Completed/Liquidated
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Content --}}
                            <div class="tab-content" id="cashierFundsTabsContent">
                                {{-- Pending Tab --}}
                                <div class="tab-pane fade show active" id="funds-pending" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>REQUESTER</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => ($v->type ?? 'fund') === 'fund' && $v->status === 'pending') as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>{{ $voucher->creator->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                            @if(auth()->user()->position === 'Cashier' || auth()->user()->isSuperAdmin())
                                                            <form action="{{ route('admin-finance.accounting.cashier.approve', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Approve Petty Cash Voucher {{ $voucher->pcv_number }}?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs" title="Approve"><i class="las la-check"></i> Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin-finance.accounting.cashier.reject', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reject Petty Cash Voucher {{ $voucher->pcv_number }}?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-xs" title="Reject"><i class="las la-times"></i> Reject</button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-check-double la-2x d-block mb-2"></i>No pending vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Ongoing Tab --}}
                                <div class="tab-pane fade" id="funds-ongoing" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>PROOF ATTACHED?</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => ($v->type ?? 'fund') === 'fund' && $v->status === 'ongoing') as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @if($voucher->proof_attachment)
                                                            <span class="badge light badge-success"><i class="las la-paperclip me-1"></i>Attached</span>
                                                        @else
                                                            <span class="badge light badge-warning">Awaiting Upload</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                             <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                            @if($voucher->proof_attachment && (auth()->user()->position === 'Cashier' || auth()->user()->isSuperAdmin()))
                                                            <form action="{{ route('admin-finance.accounting.cashier.complete', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark Petty Cash Voucher {{ $voucher->pcv_number }} as Completed?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs" title="Complete"><i class="las la-check-circle"></i> Complete</button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-info-circle la-2x d-block mb-2"></i>No ongoing vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Completed Tab --}}
                                <div class="tab-pane fade" id="funds-completed" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>STATUS</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => ($v->type ?? 'fund') === 'fund' && in_array($v->status, ['completed', 'liquidated', 'rejected'])) as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @php
                                                            $statusClass = 'badge-success';
                                                            if ($voucher->status === 'rejected') $statusClass = 'badge-danger';
                                                        @endphp
                                                        <span class="badge light {{ $statusClass }}">
                                                            {{ ucfirst($voucher->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-history la-2x d-block mb-2"></i>No history of completed, liquidated, or rejected vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Petty Cash Freight Tab --}}
                        <div class="tab-pane fade" id="freight-type" role="tabpanel">
                            {{-- Nav Tabs --}}
                            <ul class="nav nav-tabs mb-4" id="cashierFreightTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="freight-pending-tab" data-bs-toggle="tab" data-bs-target="#freight-pending" type="button" role="tab">
                                        Pending Approval 
                                        <span class="badge bg-warning ms-1 text-white">{{ $vouchers->filter(fn($v) => $v->type === 'freight' && $v->status === 'pending')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="freight-ongoing-tab" data-bs-toggle="tab" data-bs-target="#freight-ongoing" type="button" role="tab">
                                        Ongoing 
                                        <span class="badge bg-info ms-1 text-white">{{ $vouchers->filter(fn($v) => $v->type === 'freight' && $v->status === 'ongoing')->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="freight-completed-tab" data-bs-toggle="tab" data-bs-target="#freight-completed" type="button" role="tab">
                                        Completed/Liquidated
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Content --}}
                            <div class="tab-content" id="cashierFreightTabsContent">
                                {{-- Pending Tab --}}
                                <div class="tab-pane fade show active" id="freight-pending" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>REQUESTER</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => $v->type === 'freight' && $v->status === 'pending') as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>{{ $voucher->creator->name ?? 'System' }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                            @if(auth()->user()->position === 'Cashier' || auth()->user()->isSuperAdmin())
                                                            <form action="{{ route('admin-finance.accounting.cashier.approve', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Approve Petty Cash Voucher {{ $voucher->pcv_number }}?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs" title="Approve"><i class="las la-check"></i> Approve</button>
                                                            </form>
                                                            <form action="{{ route('admin-finance.accounting.cashier.reject', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reject Petty Cash Voucher {{ $voucher->pcv_number }}?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger btn-xs" title="Reject"><i class="las la-times"></i> Reject</button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-check-double la-2x d-block mb-2"></i>No pending vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Ongoing Tab --}}
                                <div class="tab-pane fade" id="freight-ongoing" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>PROOF ATTACHED?</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => $v->type === 'freight' && $v->status === 'ongoing') as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @if($voucher->proof_attachment)
                                                            <span class="badge light badge-success"><i class="las la-paperclip me-1"></i>Attached</span>
                                                        @else
                                                            <span class="badge light badge-warning">Awaiting Upload</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                             <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                            @if($voucher->proof_attachment && (auth()->user()->position === 'Cashier' || auth()->user()->isSuperAdmin()))
                                                            <form action="{{ route('admin-finance.accounting.cashier.complete', $voucher->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark Petty Cash Voucher {{ $voucher->pcv_number }} as Completed?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs" title="Complete"><i class="las la-check-circle"></i> Complete</button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-info-circle la-2x d-block mb-2"></i>No ongoing vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Completed Tab --}}
                                <div class="tab-pane fade" id="freight-completed" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md table-hover">
                                            <thead>
                                                <tr>
                                                    <th><strong>PCV NO.</strong></th>
                                                    <th><strong>DATE</strong></th>
                                                    <th><strong>PAY TO</strong></th>
                                                    <th><strong>TOTAL AMOUNT</strong></th>
                                                    <th><strong>STATUS</strong></th>
                                                    <th><strong>ACTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($vouchers->filter(fn($v) => $v->type === 'freight' && in_array($v->status, ['completed', 'liquidated', 'rejected'])) as $voucher)
                                                <tr>
                                                    <td><strong>{{ $voucher->pcv_number }}</strong></td>
                                                    <td>{{ date('M d, Y', strtotime($voucher->date)) }}</td>
                                                    <td>{{ $voucher->pay_to }}</td>
                                                    <td><strong>₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        @php
                                                            $statusClass = 'badge-success';
                                                            if ($voucher->status === 'rejected') $statusClass = 'badge-danger';
                                                        @endphp
                                                        <span class="badge light {{ $statusClass }}">
                                                            {{ ucfirst($voucher->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin-finance.petty-cash.show', [$voucher->id, 'from' => 'cashier']) }}" class="btn btn-primary btn-xs sharp" title="View"><i class="las la-eye"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4"><i class="las la-history la-2x d-block mb-2"></i>No history of completed, liquidated, or rejected vouchers.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
