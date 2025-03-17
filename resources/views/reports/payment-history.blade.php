<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Payment History Report') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta table {
            width: auto;
        }
        .meta table, .meta th, .meta td {
            border: none;
            padding: 3px;
        }
        .status-verified {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Payment History Report') }}</h1>
        <p>{{ config('app.name') }}</p>
    </div>
    
    <div class="meta">
        <table>
            <tr>
                <td><strong>{{ __('Period') }}:</strong></td>
                <td>{{ $startDate }} {{ __('to') }} {{ $endDate }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('Generated At') }}:</strong></td>
                <td>{{ $generatedAt }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('Total Records') }}:</strong></td>
                <td>{{ count($data) }}</td>
            </tr>
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Member') }}</th>
                <th>{{ __('Amount (RM)') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Date') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            
            @foreach ($data as $payment)
                @php
                    $totalAmount += $payment->amount;
                    $statusClass = 'status-' . $payment->status;
                @endphp
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->user->name }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ __($payment->payment_method) }}</td>
                    <td>{{ __($payment->payment_type) }}</td>
                    <td class="{{ $statusClass }}">{{ __($payment->status) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            
            <tr>
                <th colspan="2">{{ __('Total') }}</th>
                <th>{{ number_format($totalAmount, 2) }}</th>
                <th colspan="4"></th>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>{{ __('This is an automatically generated report.') }}</p>
    </div>
</body>
</html> 