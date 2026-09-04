<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-black">Petty Cash Replenishment Report</h4>
                        <span class="fs-12 text-muted">Summary of all petty cash disbursements for the period</span>
                    </div>
                    <form action="{{ route('admin-finance.petty-cash.summary') }}" method="GET" class="d-flex gap-2">
                        <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="card-body">

                    @if($vouchers->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="las la-file-invoice la-3x d-block mb-2"></i>
                            No petty cash vouchers found for {{ date('F Y', strtotime($selectedMonth . '-01')) }}.
                        </div>
                    @else

                    @php $grandTotal = $vouchers->sum('items_sum_amount'); @endphp

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" style="border: 2px solid #333 !important; font-size: 0.875rem;">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th style="min-width:110px;">DATE</th>
                                    <th style="min-width:120px;">PCV NO.</th>
                                    <th style="min-width:180px;">PAY TO</th>
                                    <th>PARTICULARS</th>
                                    <th class="text-end" style="min-width:130px;">TOTAL AMOUNT</th>
                                    <th class="text-center" style="min-width:100px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vouchers->sortBy('date') as $voucher)
                                <tr>
                                    <td>{{ date('m/d/Y', strtotime($voucher->date)) }}</td>
                                    <td class="fw-bold">
                                        <a href="{{ route('admin-finance.petty-cash.show', $voucher->id) }}" class="text-dark">
                                            {{ $voucher->pcv_number }}
                                        </a>
                                    </td>
                                    <td>{{ $voucher->pay_to }}</td>
                                    <td>
                                        @foreach($voucher->items as $item)
                                            <div class="small">• {{ $item->particulars }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-end fw-bold">₱ {{ number_format($voucher->items_sum_amount ?? 0, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge light {{ $voucher->status === 'liquidated' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($voucher->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="border-top: 2px solid #333;">
                                <tr class="bg-light fw-bold">
                                    <td colspan="4" class="text-end">GRAND TOTAL</td>
                                    <td class="text-end text-success fs-16">₱ {{ number_format($grandTotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @php $monthLabel = date('F Y', strtotime($selectedMonth . '-01')); @endphp
                    @if($vouchers->where('status', 'completed')->count() > 0)
                    <div class="p-4 border rounded bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-black">Ready for Liquidation?</h5>
                            <p class="mb-0 small text-muted">
                                This will close all <strong>{{ $vouchers->where('status','completed')->count() }} completed</strong> voucher(s) for {{ $monthLabel }} and post a journal entry.
                            </p>
                        </div>
                        <form action="{{ route('admin-finance.petty-cash.liquidate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <button type="submit" class="btn btn-primary rounded shadow-sm px-4 d-flex align-items-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important;" onclick="return confirm('Liquidate all completed vouchers for {{ $monthLabel }}?')">
                                <i class="las la-check-circle me-1"></i>Liquidate & Journalize
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="p-4 border rounded text-center text-muted">
                        <i class="las la-check-circle text-success me-1"></i>
                        All vouchers for <strong>{{ $monthLabel }}</strong> have already been liquidated.
                    </div>
                    @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
