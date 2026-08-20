<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ $date }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Attendance Report - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
    <tr>
        <td>{{ $user['name'] }}</td>
        @if (!empty($user['attendances']))
            @foreach ($user['attendances'] as $attendance)
                <td>{{ $attendance['clock_in'] ?? '-' }}</td>
                <td>{{ $attendance['clock_out'] ?? '-' }}</td>
                <td>{{ isset($attendance['total_hours']) ? $attendance['total_hours'] . ' Jam' : '-' }}</td>
            @endforeach
        @else
            <td>-</td>
            <td>-</td>
            <td>-</td>
        @endif
    </tr>
@endforeach
        </tbody>
    </table>
</body>
</html>
