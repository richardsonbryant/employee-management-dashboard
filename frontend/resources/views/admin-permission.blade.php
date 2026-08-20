<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Permission Dashboard</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <div class="content" id="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Alert Messages -->
                    @if(session()->has("success"))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session()->get("success") }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session()->has("error"))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session()->get("error") }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Main Card -->
                    <div class="main-card">
                        
                        <div class="today-date mb-4 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class = "fs-5"><i class="far fa-calendar-alt me-2"></i>Today's Date: {{ \Carbon\Carbon::parse($currentDate)->format('d M Y') }}</h5>
                                </div>
                                <div class="col-md-6">
                                    @if(count($cutiBersamaEvents) > 0)
                                        <h5 class="mb-2"><i class="fas fa-calendar-day me-2"></i>Upcoming Holidays:</h5>
                                        <ul class="list-group">
                                            @foreach ($cutiBersamaEvents as $cuti)
                                                <li class="list-group-item border-0 bg-transparent py-1 fs-5">
                                                    <span class="badge bg-info me-2">{{ \Carbon\Carbon::parse($cuti['date'])->format('d M Y') }}</span>
                                                    {{ $cuti['event'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted"><i class="fas fa-info-circle me-2"></i>No upcoming holidays</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Tabs -->
                        <div class="filter-tabs mb-3">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" href="{{ route('admin-dashboard') }}">All Requests</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('admin-dashboard', ['status' => 'pending']) }}">Pending</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('admin-dashboard', ['status' => 'approved']) }}">Approved</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('admin-dashboard', ['status' => 'rejected']) }}">Rejected</a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-content" style="table-layout: fixed;">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i> Name</th>
                                            <th><i class="fas fa-calendar-plus me-1"></i> Start Date</th>
                                            <th><i class="fas fa-calendar-minus me-1"></i> End Date</th>
                                            <th><i class="fas fa-calculator me-1"></i> Total Days</th>
                                            <th><i class="fas fa-comment me-1"></i> Reason</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                            <th><i class="fas fa-cog me-1"></i> Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($all_user) && $all_user->count() > 0)
                                            @foreach ($all_user as $item)
                                            <tr class="clickable-row"
                                            data-href="
                                                {{ 
                                                    $item->request_type == 'annual' ? route('EditUser', ['id' => $item->id, 'from' => 'admin-dashboard']) :
                                                    ($item->request_type == 'wfh' ? route('EditWfh', ['id' => $item->id, 'from' => 'admin-dashboard']) :
                                                    ($item->request_type == 'sick' ? route('EditSick', ['id' => $item->id, 'from' => 'admin-dashboard']) : '#'))
                                                }}
                                            ">
                                        
                                                <td class="align-middle"> 
                                                    <div class="d-flex align-items-center">
                                                        <span class="ms-2">{{$item->new_name}}</span>
                                                    </div>
                                                </td>
                                                <td class="align-middle"> {{ \Carbon\Carbon::parse($item->start_off_date)->format('d M Y') }}</td>
                                                <td class="align-middle"> {{ \Carbon\Carbon::parse($item->end_off_date)->format('d M Y') }}</td>
                                                <td class="align-middle"> 
                                                    <span class="badge bg-secondary">{{$item->total_off_day}} days</span>
                                                </td>
                                                <td class="align-middle">
                                                    <?php 
                                                        $shortReason = Str::limit($item->reason, 15); 
                                                    ?>
                                                    <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $item->reason }}">
                                                        {{ $shortReason }} 
                                                        @if (strlen($item->reason) > 15) 
                                                            <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    @if($item->approval_status == 'pending')
                                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                                    @elseif($item->approval_status == 'approved')
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Approved</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @if($item->source == 'user')
                                                        <a href="{{ route('EditUser', ['id' => $item->id, 'from' => 'admin-dashboard']) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-check-circle me-1"></i> Review
                                                        </a>
                                                    @elseif($item->source == 'wfh')
                                                        <a href="{{ route('EditWfh', ['id' => $item->id, 'from' => 'admin-dashboard']) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-check-circle me-1"></i> Review
                                                        </a>
                                                    @elseif($item->source == 'permission')
                                                        <a href="{{ route('EditSick', ['id' => $item->id, 'from' => 'admin-dashboard']) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-check-circle me-1"></i> Review
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="fas fa-folder-open text-muted mb-2" style="font-size: 2rem;"></i>
                                                        <p class="mb-0">No leave requests found</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">Showing {{ $all_user->firstItem() ?? 0 }} to {{ $all_user->lastItem() ?? 0 }} of {{ $all_user->total() ?? 0 }} entries</p>
                                </div>
                                <div>
                                    {{ $all_user->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reason Modal -->
    <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reasonModalLabel"><i class="fas fa-comment-alt me-2"></i>Leave Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullReasonText" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Reason modal
            document.querySelectorAll('.reason-text').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.stopPropagation(); // Prevent row click when clicking on reason
                    document.getElementById('fullReasonText').innerText = this.getAttribute('data-reason');
                });
            });
            
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rows = document.querySelectorAll(".clickable-row");
        rows.forEach(row => {
            row.addEventListener("click", () => {
                const href = row.getAttribute("data-href");
                if (href) window.location.href = href;
            });
        });
    });
</script>
            });
    </script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>