<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaction->uuid }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 40px;
        }
        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #111;
            font-size: 24px;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .info-col {
            width: 48%;
        }
        .info-col h3 {
            font-size: 14px;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .info-col p {
            margin: 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table th {
            background: #f9f9f9;
            text-align: left;
            padding: 12px;
            font-size: 14px;
            border-bottom: 2px solid #eee;
        }
        table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .totals {
            text-align: right;
        }
        .totals p {
            margin: 5px 0;
            font-size: 14px;
        }
        .totals .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-top: 15px;
        }
        .footer {
            margin-top: 100px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Receipt</h1>
        <p>Transaction ID: {{ $transaction->uuid }}</p>
    </div>

    <div class="info">
        <table style="border: none; margin-bottom: 0;">
            <tr>
                <td style="border: none; width: 50%; vertical-align: top; padding: 0;">
                    <h3>Billed To:</h3>
                    <p><strong>{{ $transaction->user->name }}</strong></p>
                    <p>{{ $transaction->user->email }}</p>
                </td>
                <td style="border: none; width: 50%; vertical-align: top; padding: 0; text-align: right;">
                    <h3>Transaction Details:</h3>
                    <p><strong>Date:</strong> {{ $transaction->created_at->format('F j, Y') }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($transaction->type) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center;">Quantity</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $transaction->payable && $transaction->payable->listing ? $transaction->payable->listing->title : 'Item Purchase' }}
                    @if($transaction->type === 'purchase' && isset($transaction->metadata['snapshot_price']))
                        <br><small style="color: #666;">Unit Price: {{ number_format($transaction->metadata['snapshot_price'], 2) }} €</small>
                    @endif
                </td>
                <td style="text-align: center;">{{ $transaction->quantity ?? 1 }}</td>
                <td style="text-align: right;">{{ number_format($transaction->amount, 2) }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <p>Subtotal: {{ number_format($transaction->amount, 2) }} €</p>
        @if($transaction->fee > 0)
            <p>Fee: {{ number_format($transaction->fee, 2) }} €</p>
        @endif
        <p class="grand-total">Total Paid: {{ number_format($transaction->amount + ($transaction->fee ?? 0), 2) }} €</p>
    </div>

    <div class="footer">
        <p>Thank you for choosing Finovaste.</p>
        <p>&copy; {{ date('Y') }} Finovaste. All rights reserved.</p>
    </div>
</body>
</html>
