<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Summary Employee</title>
    <link href="{{ asset('css/summary-style.css') }}" rel="stylesheet">
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
    <div class="main-content">
        <!-- Alerts -->
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

        <!-- Page Content -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-users me-2"></i>Employee Summary
                </h5>
                <div class="search-bar">
                    <form action="{{ route('summaryEmployee') }}" method="GET" class="d-flex">
                        <div class="position-relative w-100">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Leave Quota</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($employee) && $employee->count() > 0)
                                @foreach ($employee as $item)
                                    <tr class="clickable" onclick="window.location='{{ route('viewUserData', ['email' => $item['email']]) }}'">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-secondary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                    <small class="text-muted">{{ $item['email'] ?? 'No email' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item['leaveQuota'] }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('viewUserData', ['email' => $item['email']]) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" title="View Details">
                                                <i class="fas fa-info-circle info-icon"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5>No Employees Found</h5>
                                            <p class="text-muted">Try a different search term or add new employees.</p>
                                            <a href="/register" class="btn btn-primary btn-sm mt-2">
                                                <i class="fas fa-plus me-1"></i> Add Employee
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInside = sidebar.contains(event.target) || sidebarToggle.contains(event.target);
                
                if (!isClickInside && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });
            
            // Dropdown functionality for sidebar
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle.sidebar-link');
            
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.classList.toggle('show');
                });
            });
        });
        </script> --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>