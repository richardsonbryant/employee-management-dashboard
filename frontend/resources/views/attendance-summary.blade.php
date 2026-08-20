<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Summary Attendance Employee</title>
    <link href="{{ asset('css/summary-attendance.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
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
    
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-user pt-4 pb-3 mb-3 text-center border-bottom">
                <div class="avatar mb-2">
                    <i class="fas fa-user-circle fa-3x text-white-50"></i>
                </div>
                <div class="user-info">
                    <span class="text-white">{{ Auth::user()->role === 'admin' ? 'Administrator' : 'Employee' }}</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="sidebar-header">
                    <span>MAIN NAVIGATION</span>
                </div>
                <a href="/summary-employee" class="sidebar-link">
                    <i class="fas fa-users me-2"></i> Employee Summary
                </a>
                <a href="/summary-attendance" class="sidebar-link">
                    <i class="fas fa-clipboard-check me-2"></i> Attendance Summary
                </a>
    
                <div class="dropdown">
                    <a class="dropdown-toggle sidebar-link" href="#" role="button" id="leaveDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar-alt me-2"></i> Request Leave
                    </a>
                    <ul class="dropdown-menu sidebar-dropdown" aria-labelledby="leaveDropdown">
                        <li>
                            <a class="dropdown-item text-secondary" href="/add/user">
                                <i class="fas fa-umbrella-beach me-2"></i> Annual Leave
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-secondary" href="/add/sick-user">
                                <i class="fas fa-procedures me-2"></i> Sick Leave
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-secondary" href="/add/wfh-user">
                                <i class="fas fa-home me-2"></i> Work From Home
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-secondary" href="/add/noPermissionUser">
                                <i class="fas fa-exclamation-triangle me-2"></i> No Permission
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="/broadcast" class="sidebar-link">
                    <i class="fas fa-bullhorn me-2"></i> Cuti Bersama
                </a>
                <a href="/register" class="sidebar-link">
                    <i class="fas fa-user-plus me-2"></i> Add New Employee
                </a>
            </nav>
        </div>

     <!-- Main Content -->
  <div class="main-content">
    @if(session()->has("success"))
    <div class="alert alert-success">
      <i class="fas fa-check-circle me-2"></i> {{session()->get("success")}}
    </div>
    @endif
    @if(session()->has("error"))
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-circle me-2"></i> {{session()->get("error")}}
    </div>
    @endif

    <div class="content-card">
      <div class="card-header">
        <h1 class="card-title">
          <i class="fas fa-clipboard-check me-2"></i> Employee Attendance
        </h1>
        <div class="header-actions">
          <a href="{{ route('exportAttendance', ['date' => $date]) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Export to PDF
          </a>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
          <form method="GET" action="{{ route('summaryAttendance') }}" class="date-filter-form">
            <div class="form-group">
              <label for="date" class="form-label">Select Date:</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-calendar"></i>
                </span>
                <input 
                  type="date" 
                  id="date" 
                  name="date" 
                  value="{{ $date }}" 
                  class="form-control" 
                  onchange="this.form.submit()" 
                  max="{{ \Carbon\Carbon::today()->toDateString() }}"
                >
              </div>
              <small class="form-text text-muted">Select a date to view attendance records</small>
            </div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Employee Name</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Total Hours</th>
              <th>Verification</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <i class="fas fa-user-circle me-2 text-secondary"></i>
                  <span>{{ $user['name'] }}</span>
                </div>
              </td>
              @php
                $attendance = $user['attendances'][0] ?? null;
              @endphp
              <td>
                @if(isset($attendance['clock_in']))
                  <span class="text-success">
                    <i class="fas fa-sign-in-alt me-1"></i> {{ $attendance['clock_in'] }}
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if(isset($attendance['clock_out']))
                  <span class="text-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> {{ $attendance['clock_out'] }}
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if(isset($attendance['total_hours']))
                  <span class="badge bg-info">
                    <i class="fas fa-clock me-1"></i> {{ $attendance['total_hours'] }} Hours
                  </span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($attendance && $attendance['verification'])
                  <a href="{{ asset('storage/' . $attendance['verification']) }}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-image me-1"></i> View Photo
                  </a>
                @else
                  <span class="text-muted">
                    <i class="fas fa-ban me-1"></i> No Photo
                  </span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>