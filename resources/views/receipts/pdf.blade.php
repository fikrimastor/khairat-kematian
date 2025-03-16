<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Receipt') }} #{{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        .receipt-info {
            margin-bottom: 30px;
        }
        .receipt-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-date {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            font-size: 14px;
        }
        .info-value {
            flex: 1;
            font-size: 14px;
        }
        .payment-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .payment-details th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
        }
        .payment-details td {
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #ddd;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px dotted #aaa;
            width: 200px;
            text-align: center;
            padding-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">{{ config('app.name') }}</div>
            <div class="subtitle">{{ __('Khairat Kematian Receipt') }}</div>
        </div>
        
        <div class="receipt-info">
            <div class="receipt-number">{{ __('Receipt') }} #{{ $receipt->receipt_number }}</div>
            <div class="receipt-date">{{ __('Date') }}: {{ $receipt->formattedDate }}</div>
        </div>
        
        <div class="section">
            <div class="section-title">{{ __('Member Information') }}</div>
            <div class="info-row">
                <div class="info-label">{{ __('Name') }}:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('ID Number') }}:</div>
                <div class="info-value">{{ $user->identification_number ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('Email') }}:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('Phone') }}:</div>
                <div class="info-value">{{ $user->phone ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('Address') }}:</div>
                <div class="info-value">{{ $user->address ?? '-' }}</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">{{ __('Payment Details') }}</div>
            <table class="payment-details">
                <thead>
                    <tr>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Year') }}</th>
                        <th>{{ __('Household Count') }}</th>
                        <th>{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ __('Khairat Kematian Contribution') }}</td>
                        <td>{{ $payment->year }}</td>
                        <td>{{ $payment->household_count }}</td>
                        <td>RM {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">{{ __('Total') }}</td>
                        <td>RM {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="info-row">
                <div class="info-label">{{ __('Payment Method') }}:</div>
                <div class="info-value">{{ $payment->payment_method }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('Reference Number') }}:</div>
                <div class="info-value">{{ $payment->reference_no ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('Payment Status') }}:</div>
                <div class="info-value">{{ $payment->status }}</div>
            </div>
        </div>
        
        <div class="signature">
            {{ __('Authorized Signature') }}
        </div>
        
        <div class="footer">
            <p>{{ __('This is a computer-generated receipt and does not require a physical signature.') }}</p>
            <p>{{ __('For any inquiries, please contact us at') }} {{ config('app.email', 'info@khairat-kematian.org') }}</p>
        </div>
    </div>
</body>
</html> 