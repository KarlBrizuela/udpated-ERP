<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JV Request #{{ $request->jv_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 20px; color: #333; }
        .form-container { border: 2px solid #000; padding: 20px; max-width: 850px; margin: auto; position: relative; background: #f0f7ff; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { width: 60px; height: 60px; margin-right: 15px; }
        .company-info h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .company-info p { margin: 2px 0; font-size: 11px; }
        .form-title { text-align: center; font-weight: bold; background: #000; color: #fff; padding: 5px; margin-bottom: 15px; font-size: 14px; }
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .meta-item { font-size: 13px; border-bottom: 1px solid #000; flex: 1; margin: 0 10px; padding: 2px 5px; }
        .meta-label { font-weight: bold; margin-right: 5px; }
        .form-body { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid #000; margin-bottom: 15px; }
        .col { padding: 10px; border-right: 1px solid #000; min-height: 200px; }
        .col:last-child { border-right: none; }
        .section-label { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .reason-text { font-size: 14px; line-height: 1.6; }
        .footer-grid { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid #000; }
        .sig-box { padding: 10px; border-right: 1px solid #000; border-bottom: 1px solid #000; height: 60px; display: flex; flex-direction: column; justify-content: flex-end; }
        .sig-box:last-child { border-right: none; }
        .sig-line { border-top: 1px solid #000; text-align: center; font-size: 10px; margin-top: 5px; padding-top: 2px; }
        .jv-no-watermark { position: absolute; bottom: 10px; right: 20px; font-size: 20px; font-weight: bold; color: #d00; }
        @media print { body { padding: 0; background: #fff; } .form-container { background: #fff; border: 2px solid #000; box-shadow: none; } button { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="form-container">
        <div class="header">
            <div class="company-info">
                <h2>Claretian Communications Foundation, Inc.</h2>
                <p>No. 8 Mayumi Street, U.P. Village, Diliman, Quezon City</p>
            </div>
            <div style="flex:1; text-align:right">
                <p>Date: <strong>{{ \Carbon\Carbon::parse($request->date)->format('F d, Y') }}</strong></p>
            </div>
        </div>

        <div class="form-title">REQUEST FOR ADJUSTMENTS / REVISIONS</div>

        <div class="meta-row">
            <div class="meta-item"><span class="meta-label">Client's Name:</span> 
                @foreach($request->items as $item)
                    {{ $item->customer_name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-item"><span class="meta-label">Documents:</span> {{ $request->documents ?? 'Billing Request' }}</div>
        </div>

        <div class="form-body">
            <div class="col">
                <span class="section-label">Reason:</span>
                <div class="reason-text">
                    {{ $request->reason ?? 'To record requested adjustments.' }}
                    <br><br>
                    <strong>Summary Breakdown:</strong>
                    @foreach($request->items as $item)
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-top:5px;">
                            <span>{{ $item->reference_no }} - {{ $item->customer_name }}</span>
                            <span>₱ {{ number_format($item->amount, 2) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div style="display:flex; justify-content:space-between; font-weight:bold;">
                        <span>Total:</span>
                        <span>₱ {{ number_format($request->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <span class="section-label">Accounting Department Remarks:</span>
                <p>{{ $request->accounting_remarks }}</p>
            </div>
        </div>

        <div class="footer-grid">
            <div class="sig-box">
                <div class="sig-line">Requested by: <strong>{{ strtoupper($request->requestor->name ?? '') }}</strong></div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Accounting Department:</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Approved by: <strong>FINANCE MANAGER</strong></div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Noted by:</div>
            </div>
        </div>

        <div class="jv-no-watermark">JV No. {{ $request->jv_number }}</div>
    </div>
</body>
</html>
