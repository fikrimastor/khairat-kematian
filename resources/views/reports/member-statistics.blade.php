<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Member Statistics Report') }}</title>
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
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-box {
            width: 45%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .stat-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Member Statistics Report') }}</h1>
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
                <th>{{ __('Metric') }}</th>
                <th>{{ __('Value') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ __(str_replace('_', ' ', $key)) }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>{{ __('This is an automatically generated report.') }}</p>
    </div>
</body>
</html> 