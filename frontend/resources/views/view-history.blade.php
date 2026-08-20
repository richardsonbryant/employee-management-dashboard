<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Employee History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="{{ asset('css/view-user.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  </head>
  <body class="bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="navbar-left">
                {{-- <button id="sidebarToggle" class="btn btn-link text-white me-3">
                    <i class="fas fa-bars"></i>
                </button> --}}
                <a class="navbar-brand d-flex align-items-center" href="{{ Auth::user()->role === 'admin' ? route('admin-dashboard') : route('employee-dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> My Dashboard
                </a>
            </div>
            <div class="navbar-right">
                <div class="dropdown me-3">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>


    <div class="container mt-4">
       
        <!-- Display success and error messages -->
        @if(session()->has("success"))
            <div class="alert alert-success">
                {{ session()->get("success") }}
            </div>
        @endif
        @if(session()->has("error"))
            <div class="alert alert-danger">
                {{ session()->get("error") }}
            </div>
        @endif

        <div class="d-flex justify-content-center align-items-center mb-3 position-relative">
            <a href="{{ Auth::user()->role === 'employee' ? route('employee-dashboard') : route('admin-dashboard') }}"class="back-button position-absolute start-0">←</a>
            <h3 class ="header-name">{{ $user['name'] }}'s Information </h3>
        </div>

        <form action="{{ route('viewHistoryData', $user['email']) }}" method="GET" class="mb-3">
            <select name="year" class="form-select mb-2" onchange="this.form.submit()">
                <option value="">Select Year</option>
                @foreach(range(2024, date('Y')) as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>

            <select name="month" class="form-select mb-2" onchange="this.form.submit()">
                <option value="">Select Month</option>
                @foreach(range(1, 12) as $month)
                    <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                @endforeach
            </select>

            <select name="requestType" class="form-select" onchange="this.form.submit()">
                <option value="">Select Request Type</option>
                <option value="annual" {{ request('requestType') == 'annual' ? 'selected' : '' }}>Annual Leaves</option>
                <option value="wfh" {{ request('requestType') == 'wfh' ? 'selected' : '' }}>Work From Home</option>
                <option value="sick" {{ request('requestType') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
            </select>
        </form>

        @if(request('requestType') == 'annual' || request('requestType') == '')
        <h5 class="text-xl font-semibold !text-black"> <u> Annual Leave Requests  </u></h5>
        <table class="table table-content" style="table-layout: fixed;">
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
                @foreach ($annualLeaves as $leave)
                <tr class="clickable-row 
                {{ $leave['approval_status'] === 'approved' ? 'table-success' : ($leave['approval_status'] === 'rejected' ? 'table-danger' : '') }}" 
                data-href="{{ route('loadEmployeeRequest', ['id' => $leave['id']]) }}?type=annual&from=view-history" 
                style="cursor: pointer;">
            

                        <td>{{ $leave['new_name'] }}</td>
                        <td> {{ \Carbon\Carbon::parse($leave['start_off_date'])->format('d - m - Y') }}</td>
                        <td> {{ \Carbon\Carbon::parse($leave['end_off_date'])->format('d - m - Y') }}</td>
                        <td>{{ $leave['total_off_day'] }}</td>
                        <td>
                            <?php 
                                $shortReason = Str::limit($leave['reason'], 15); 
                            ?>
                            <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $leave['reason'] }}">
                                {{ $shortReason }} 
                                @if (strlen($leave['reason']) > 15) 
                                <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                @endif
                            </span>
                        </td>
                        <td>{{ $leave['approval_status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $annualLeaves->appends(request()->query())->links() }}
        @endif

        @if(request('requestType') == 'wfh' || request('requestType') == '')
        <h5 class="text-xl font-semibold !text-black"> <u> Work From Home Requests  </u></h5>
        <table class="table table-striped table-content" style="table-layout: fixed;">
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
                @foreach ($wfhRequests as $wfh)
                <tr class="clickable-row 
                {{ $wfh['approval_status'] === 'approved' ? 'table-success' : ($wfh['approval_status'] === 'rejected' ? 'table-danger' : '') }}" 
                data-href="{{ route('loadEmployeeRequest', ['id' => $wfh['id']]) }}?type=wfh&from=view-history" 
                style="cursor: pointer;">
            
                        <td>{{ $wfh['new_name'] }}</td>
                        <td> {{ \Carbon\Carbon::parse($wfh['start_off_date'])->format('d - m - Y') }}</td>
                        <td> {{ \Carbon\Carbon::parse($wfh['end_off_date'])->format('d - m - Y') }}</td>
                        <td>{{ $wfh['total_off_day'] }}</td>
                        <td>
                            <?php 
                                $shortReason = Str::limit($wfh['reason'], 15); 
                            ?>
                            <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $wfh['reason'] }}">
                                {{ $shortReason }} 
                                @if (strlen($wfh['reason']) > 15) 
                                <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                @endif
                            </span>
                        </td>
                        <td>{{ $wfh['approval_status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $wfhRequests->appends(request()->query())->links() }}
        @endif

        @if(request('requestType') == 'sick' || request('requestType') == '')
        <h5 class="text-xl font-semibold !text-black"> <u>Sick Requests</u></h5>
        <table class="table table-striped table-content" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Start Off</th>
                    <th>End Off</th>
                    <th>Total Off</th>
                    <th>Reason</th>
                    <th>Surat Dokter</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sickRequests as $sick)
                <tr class="clickable-row 
                {{ $sick['approval_status'] === 'approved' ? 'table-success' : ($sick['approval_status'] === 'rejected' ? 'table-danger' : '') }}" 
                data-href="{{ route('loadEmployeeRequest', ['id' => $sick['id']]) }}?type=sick&from=view-history" 
                style="cursor: pointer;">
                        <td>{{ $sick['new_name'] }}</td>
                        <td> {{ \Carbon\Carbon::parse($sick['start_off_date'])->format('d - m - Y') }}</td>
                        <td> {{ \Carbon\Carbon::parse($sick['end_off_date'])->format('d - m - Y') }}</td>
                        <td>{{ $sick['total_off_day'] }}</td>
                        <td>
                            <?php 
                                $shortReason = Str::limit($sick['reason'], 15); 
                            ?>
                            <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $sick['reason'] }}">
                                {{ $shortReason }} 
                                @if (strlen($sick['reason']) > 15) 
                                <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                @endif
                            </span>
                        </td>
                        <td>
                            <?php 
                                $shortLetter = Str::limit($sick['permission_letter'], 15); 
                            ?>
                            <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $sick['permission_letter'] }}">
                                {{ $shortLetter }} 
                                @if (strlen($sick['permission_letter']) > 15) 
                                <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                @endif
                            </span>
                        </td>
                        <td>{{ $sick['approval_status'] }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $sickRequests->appends(request()->query())->links() }}
        @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reasonModalLabel">Full Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullReasonText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function () {
                    const url = this.getAttribute('data-href');
                    if (url) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>