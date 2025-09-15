<!DOCTYPE html>
<html>
<head>
    <title>Payments Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 14px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .table th { background-color: #f8f9fa; }
        tfoot tr td { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Payments Report</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Course</th>
                <th>Amount (USD)</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($payments as $payment)
                @php $totalAmount += $payment->amount_cents / 100; @endphp
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->user->name ?? 'N/A' }}</td>
                    <td>{{ $payment->course->title ?? 'N/A' }}</td>
                    <td>${{ number_format($payment->amount_cents / 100, 2) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td>${{ number_format($totalAmount, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
