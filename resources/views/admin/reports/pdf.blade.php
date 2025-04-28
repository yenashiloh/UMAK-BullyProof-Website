<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cyberbullying Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #164789;
        }
        .header h1 {
            color: #164789;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin-top: 0;
        }
        .summary-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            width: 30%;
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .stat-box h3 {
            margin-top: 0;
            color: #164789;
        }
        .stat-box p {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        .chart-container {
            margin-bottom: 30px;
            text-align: center;
        }
        .chart-container h2 {
            color: #164789;
            margin-bottom: 15px;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #888;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #164789;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BullyProof Report</h1>
        <p>Generated on: {{ \Carbon\Carbon::parse($reportDate)->timezone('Asia/Manila')->format('F d, Y h:i A') }}</p>
        <p>Period: {{ date('F d, Y', strtotime($startDate)) }} - {{ date('F d, Y', strtotime($endDate)) }}</p>
    </div>
    
    <div class="summary-box">
        <h2>Report Summary</h2>
        <p>This report provides an overview of cyberbullying incidents reported during the specified period. It includes statistics on the number of reports, types of cyberbullying, and platforms where incidents occurred.</p>
    </div>
    
    <h2>Key Statistics</h2>
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Users</h3>
            <p>{{ number_format($totalUsers) }}</p>
        </div>
        <div class="stat-box">
            <h3>Total Reports</h3>
            <p>{{ number_format($totalReports) }}</p>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <h2>Status Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                {{-- <th>Percentage</th> --}}
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>For Review</td>
                <td>{{ number_format($toReviewCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($toReviewCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Under Investigation</td>
                <td>{{ number_format($underInvestigationCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($underInvestigationCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Awaiting Response</td>
                <td>{{ number_format($awaitingResponseCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($awaitingResponseCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Under Mediation</td>
                <td>{{ number_format($underMediationCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($underMediationCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Resolved</td>
                <td>{{ number_format($resolvedCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($resolvedCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Reopened</td>
                <td>{{ number_format($reopenedCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($reopenedCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Dismissed</td>
                <td>{{ number_format($dismissedCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($dismissedCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
            <tr>
                <td>Withdrawn</td>
                <td>{{ number_format($withdrawnCount) }}</td>
                {{-- <td>{{ $totalReports > 0 ? number_format(($withdrawnCount / $totalReports) * 100, 2) : 0 }}%</td> --}}
            </tr>
        </tbody>
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    
    <div class="chart-container">
        <h2>Number of Reports Over Time</h2>
        <img src="{{ $lineChartImage }}" alt="Monthly Reports" width="600" height="300">
    </div>
    
    <div class="chart-container">
        <h2>Types of Cyberbullying</h2>
        <img src="{{ $cyberbullyingPieChartImage }}" alt="Types of Cyberbullying" width="600" height="300">
    </div>
    
    <div class="chart-container">
        <h2>Cyberbullying Platforms</h2>
        <img src="{{ $platformBarChartImage }}" alt="Cyberbullying Platforms Chart" width="600" height="300">
    </div>
    
    <div class="footer">
        <p>This report is confidential and generated for administrative purposes only.</p>
    </div>
</body>
</html>