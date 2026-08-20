<!DOCTYPE html>
<html>
<head>
    <title>User Data PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; }
    </style>
</head>
<body>
    <h3>{{ $user['name'] }}'s Data</h3>

    @if($requestType == 'annual' || $requestType == '')
    <h4>Annual Leave Requests</h4>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Off</th>
                <th>End Off</th>
                <th>Total Off</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($annualLeaves as $leave)
                <tr>
                    <td>{{ $leave['new_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave['start_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave['end_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ $leave['total_off_day'] }}</td>
                    <td>{{ $leave['reason'] }}</td>
                    <td>{{ $leave['approval_status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($requestType == 'wfh' || $requestType == '')
    <h4>Work From Home Requests</h4>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Off</th>
                <th>End Off</th>
                <th>Total Off</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wfhRequests as $wfh)
                <tr>
                    <td>{{ $wfh['new_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($wfh['start_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($wfh['end_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ $wfh['total_off_day'] }}</td>
                    <td>{{ $wfh['reason'] }}</td>
                    <td>{{ $wfh['approval_status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($requestType == 'sick' || $requestType == '')
    <h4>Sick Leave Requests</h4>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Off</th>
                <th>End Off</th>
                <th>Total Off</th>
                <th>Reason</th>
                <th>Doctor's Letter</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sickRequests as $sick)
                <tr>
                    <td>{{ $sick['new_name'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($sick['start_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($sick['end_off_date'])->format('d-m-Y') }}</td>
                    <td>{{ $sick['total_off_day'] }}</td>
                    <td>{{ $sick['reason'] }}</td>
                    <td>{{ $sick['permission_letter'] }}</td>
                    <td>{{ $sick['approval_status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
