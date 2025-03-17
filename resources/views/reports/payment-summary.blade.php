<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Payment Summary Report') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            padding: 8px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Payment Summary Report') }}</h1>
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
        </table>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>{{ __('Payment Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Total Payments') }}</th>
                <th>{{ __('Total Amount (RM)') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPayments = 0;
                $totalAmount = 0;
            @endphp
            
            @foreach ($data as $row)
                @php
                    $totalPayments += $row->total_payments;
                    $totalAmount += $row->total_amount;
                @endphp
                <tr>
                    <td>{{ __($row->payment_type) }}</td>
                    <td>{{ __($row->status) }}</td>
                    <td>{{ $row->total_payments }}</td>
                    <td>{{ number_format($row->total_amount, 2) }}</td>
                </tr>
            @endforeach
            
            <tr>
                <th colspan="2">{{ __('Total') }}</th>
                <th>{{ $totalPayments }}</th>
                <th>{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>{{ __('This is an automatically generated report.') }}</p>
    </div>
</body>
</html> 