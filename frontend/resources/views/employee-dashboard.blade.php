<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Dashboard</title>
    <link href="{{ asset('css/employee-dashboard.css') }}" rel="stylesheet">
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
                <!-- Mail icon with notification badge -->
                <form action="{{ route('mailbox') }}" class="me-3">
                    @php
                    $unreadMessages = $broadcasts->filter(function($broadcast) {
                        $responses = $broadcast['responses'] ?? [];
                        return !collect($responses)->contains(function ($response) {
                            return $response['user_id'] == Auth::id();
                        });
                    })->count();
                    @endphp

                    <button type="submit" class="btn btn-link position-relative p-0">
                        <i class="fas fa-envelope text-white" style="font-size: 1.2rem;"></i>
                        @if($unreadMessages > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadMessages }}
                            </span>
                        @endif
                    </button>
                </form>
                
                <!-- User dropdown menu -->
                <div class="dropdown">
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
                <span class="text-white">{{ Auth::user()->name }}</span>
                <div class="text-white-50 small">Employee</div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-header">
                <span>MAIN NAVIGATION</span>
            </div>
            
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
                </ul>
            </div>
            
            <a href="{{ route('viewHistoryData', ['email' => Auth::user()->email]) }}" class="sidebar-link">
                <i class="fas fa-history me-2"></i> Leave History
            </a>
{{-- 
            <a href="{{ route('attendanceReports') }}" class="sidebar-link">
                <i class="fas fa-chart-line me-2"></i> Attendance Reports
            </a> --}}
{{-- 
            <a href="{{ route('teamCalendar') }}" class="sidebar-link">
                <i class="fas fa-users me-2"></i> Team Calendar
            </a> --}}

            {{-- <div class="sidebar-header mt-3">
                <span>SETTINGS</span>
            </div>

            <a href="{{ route('profileSettings') }}" class="sidebar-link">
                <i class="fas fa-user-cog me-2"></i> Profile Settings
            </a> --}}

            {{-- <a href="{{ route('notifications') }}" class="sidebar-link">
                <i class="fas fa-bell me-2"></i> Notifications
            </a> --}}
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

                    <!-- Summary Cards Row -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="main-card h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                                        <h5 class="fw-bold">Leave Balance</h5>
                                        <h2 class="mt-2 mb-0">{{ $leave_quota }}</h2>
                                        <p class="text-muted">Days Available</p>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($leave_quota/12)*100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="main-card h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div class="mb-2">
                                        <i class="fas fa-business-time fa-3x text-success mb-3"></i>
                                        <h5 class="fw-bold">Today's Status</h5>
                                        <div class="status-indicator mt-2 {{ $clockedIn ? 'clocked-in' : 'clocked-out' }}">
                                            <span class="dot"></span>
                                            <span>{{ $clockedIn ? 'Clocked In' : 'Not Clocked In' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">
                                        @if($clockedIn && isset($clockedInTime))
                                            Last clock-in: {{ \Carbon\Carbon::parse($clockedInTime)->format('h:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="main-card h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <div class="mb-2">
                                        <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
                                        <h5 class="fw-bold">Pending Requests</h5>
                                        <h2 class="mt-2 mb-0">{{ $pendingRequests }}</h2>
                                        <p class="text-muted">Awaiting Approval</p>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                    </div> 

                    <!-- Time & Attendance Card -->
                    <div class="card attendance-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h4 class="m-0"><i class="fas fa-clock me-2"></i> Time & Attendance</h4>
                            <h5 class="m-0 current-time" id="current-time"></h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap button-clock justify-content-center gap-3">
                                <!-- Clock In Button -->
                                <button type="button" id="start-clock-in-btn" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Clock In
                                </button>
                        
                                <!-- Video and Canvas for capturing image -->
                                <div class="camera-container" style="display: none;">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <video id="video" autoplay class="img-fluid rounded mb-3 border"></video>
                                            <canvas id="canvas" style="display: none;"></canvas>
                                            <button type="button" id="capture-btn" class="btn btn-warning">
                                                <i class="fas fa-camera me-2"></i> Take Photo
                                            </button>
                                            <button type="button" id="retake-btn" class="btn btn-outline-secondary ms-2" style="display: none;">
                                                <i class="fas fa-redo me-2"></i> Retake
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        
                                <!-- Clock In Form -->
                                <form id="clock-in-form" action="{{ route('clock-in') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <input type="hidden" name="verification" id="verification">
                                    <button type="submit" id="clock-in-btn" class="btn btn-success btn-lg" disabled>
                                        <i class="fas fa-check-circle me-2"></i> Confirm Clock In
                                    </button>
                                    <button type="button" id="cancel-clock-in-btn" class="btn btn-outline-danger btn-lg ms-2">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </button>
                                </form>
                            
                                <!-- Clock Out Button -->
                                <form action="{{ route('clock-out') }}" method="POST">
                                    @csrf
                                    <button type="submit" id="clock-out-btn" class="btn btn-danger btn-lg" {{ !$clockedIn || $clockedOut ? 'disabled' : '' }}>
                                        <i class="fas fa-sign-out-alt me-2"></i> Clock Out
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Location info (for development only) -->
                            <div class="row mt-4 d-none">
                                <div class="col-md-8 offset-md-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="mb-0" id="device-lat">Latitude: Loading...</p>
                                            <p class="mb-0" id="device-lng">Longitude: Loading...</p>
                                            <p class="mb-0">Office Lat: {{ config('app.office_latitude') }}</p>
                                            <p class="mb-0">Office Lng: {{ config('app.office_longitude') }}</p>
                                            <p class="mb-0">Radius: {{ config('app.office_radius') }}m</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Summary Card -->
                    <div class="main-card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h4 class="m-0"><i class="fas fa-clipboard-list me-2"></i> My Leave Requests</h4>
                            <div class="leave-quota">
                                <div class="d-flex align-items-center">
                                    {{-- <div class="progress flex-grow-1 me-2" style="height: 10px; width: 100px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($leave_quota/20)*100 }}%"></div>
                                    </div> --}}
                                    {{-- <span class="badge bg-primary fs-6">
                                        <i class="fas fa-calendar-check me-1"></i> {{$leave_quota}} days left
                                    </span> --}}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Tabs -->
                        <div class="filter-tabs">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" href="{{ route('employee-dashboard') }}">All Requests</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('employee-dashboard', ['status' => 'pending']) }}">Pending</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('employee-dashboard', ['status' => 'approved']) }}">Approved</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" href="{{ route('employee-dashboard', ['status' => 'rejected']) }}">Rejected</a>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($user_data->count() > 0)
                                            @foreach ($user_data as $item)
                                                <tr class="clickable-row" onclick="window.location='{{ route('loadEmployeeRequest', ['id' => $item['id']]) . '?type=' . $item['request_type'] . '&from=employee-dashboard' }}'">
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <span class="ms-2">{{ $item['new_name'] ?? '-' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle">{{ \Carbon\Carbon::parse($item['start_off_date'])->format('d M Y') }}</td>
                                                    <td class="align-middle">{{ \Carbon\Carbon::parse($item['end_off_date'])->format('d M Y') }}</td>
                                                    <td class="align-middle">
                                                        <span class="badge bg-secondary">{{ $item['total_off_day'] ?? '0' }} days</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php 
                                                            $shortReason = Str::limit($item['reason'], 15); 
                                                        ?>
                                                        <span class="reason-text" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reason="{{ $item['reason'] }}">
                                                            {{ $shortReason }} 
                                                            @if (strlen($item['reason']) > 15) 
                                                                <i class="fas fa-ellipsis-h text-muted ms-1"></i>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($item['approval_status'] == 'pending')
                                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                                        @elseif($item['approval_status'] == 'approved')
                                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Approved</span>
                                                        @else
                                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Rejected</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
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
                                    <p class="text-muted mb-0">Showing {{ $user_data->firstItem() ?? 0 }} to {{ $user_data->lastItem() ?? 0 }} of {{ $user_data->total() ?? 0 }} entries</p>
                                </div>
                                <div>
                                    {{ $user_data->links() }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const backdrop = document.getElementById('backdrop');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                });
            }
            
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }
            
            // Handle reason modal
            const reasonTexts = document.querySelectorAll('.reason-text');
            reasonTexts.forEach(item => {
                item.addEventListener('click', function() {
                    const fullReason = this.getAttribute('data-reason');
                    document.getElementById('fullReasonText').innerText = fullReason;
                });
            });
            
            // Clock functionality
            function updateClock() {
                let now = new Date();
                let timeString = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
                document.getElementById("current-time").innerText = "Current Time: " + timeString;
            }
            setInterval(updateClock, 1000);
            updateClock();
            
            // Camera handling for clock-in
            const startClockInBtn = document.getElementById("start-clock-in-btn");
            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const captureButton = document.getElementById("capture-btn");
            const retakeButton = document.getElementById("retake-btn");
            const clockInButton = document.getElementById("clock-in-btn");
            const cancelClockInBtn = document.getElementById("cancel-clock-in-btn");
            const verificationInput = document.getElementById("verification");
            const cameraContainer = document.querySelector(".camera-container");
            const clockInForm = document.getElementById("clock-in-form");
            let stream = null;
    
            // Initiate clock-in process
            if (startClockInBtn) {
                startClockInBtn.addEventListener("click", function() {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(mediaStream => {
                            stream = mediaStream;
                            video.srcObject = stream;
                            cameraContainer.style.display = "block";
                            clockInForm.style.display = "block";
                            startClockInBtn.style.display = "none";
                        })
                        .catch(error => {
                            alert("Camera permission is required for clock-in.");
                            console.error("Camera error:", error);
                        });
                });
            }
    
            // Take photo for verification
            if (captureButton) {
                captureButton.addEventListener("click", function() {
                    const context = canvas.getContext("2d");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
                    // Convert to Base64 and store in hidden input
                    const imageData = canvas.toDataURL("image/png");
                    verificationInput.value = imageData;
    
                    // Show captured image
                    video.style.display = "none";
                    canvas.style.display = "block";
                    captureButton.style.display = "none";
                    retakeButton.style.display = "inline-block";
                    clockInButton.disabled = false;
                });
            }
            
            // Retake photo
            if (retakeButton) {
                retakeButton.addEventListener("click", function() {
                    video.style.display = "block";
                    canvas.style.display = "none";
                    captureButton.style.display = "inline-block";
                    retakeButton.style.display = "none";
                    clockInButton.disabled = true;
                    verificationInput.value = "";
                });
            }
            
            // Cancel clock-in process
            if (cancelClockInBtn) {
                cancelClockInBtn.addEventListener("click", function() {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                    cameraContainer.style.display = "none";
                    clockInForm.style.display = "none";
                    startClockInBtn.style.display = "inline-block";
                    video.style.display = "block";
                    canvas.style.display = "none";
                    captureButton.style.display = "inline-block";
                    retakeButton.style.display = "none";
                });
            }
            
            // Get geolocation
            if (navigator.geolocation) {
                navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
                    if (result.state === "denied") {
                        alert("Location permission has been denied. Please enable location access in your browser settings.");
                    }
                });
    
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
    
                        if (document.getElementById("latitude")) {
                            document.getElementById("latitude").value = userLat;
                        }
                        if (document.getElementById("longitude")) {
                            document.getElementById("longitude").value = userLng;
                        }
                        
                        // For development display
                        if (document.getElementById("device-lat")) {
                            document.getElementById("device-lat").innerText = "Device Latitude: " + userLat;
                        }
                        if (document.getElementById("device-lng")) {
                            document.getElementById("device-lng").innerText = "Device Longitude: " + userLng;
                        }
    
                        const officeLat = {{ config('app.office_latitude') ?? 0 }};
                        const officeLng = {{ config('app.office_longitude') ?? 0 }};
                        const officeRadius = {{ config('app.office_radius') ?? 100 }};
    
                        function getDistance(lat1, lon1, lat2, lon2) {
                            const R = 6371e3; // Earth radius in meters
                            const φ1 = lat1 * Math.PI / 180;
                            const φ2 = lat2 * Math.PI / 180;
                            const Δφ = (lat2 - lat1) * Math.PI / 180;
                            const Δλ = (lon2 - lon1) * Math.PI / 180;
                            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                                    Math.cos(φ1) * Math.cos(φ2) *
                                    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                            return R * c;
                        }
    
                        const distance = getDistance(userLat, userLng, officeLat, officeLng);
                        
                        if (document.getElementById("clock-in-btn")) {
                            if (distance <= officeRadius) {
                                clockInButton.disabled = true; // Still needs photo verification
                            } else {
                                alert("You are outside the office location. Clock-in is not allowed.");
                            }
                        }
                    },
                    function(error) {
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                alert("Location permission denied. Please allow location access in your browser settings.");
                                break;
                            case error.POSITION_UNAVAILABLE:
                                alert("Location information is unavailable. Please ensure GPS or location services are enabled.");
                                break;
                            case error.TIMEOUT:
                                alert("Location request timed out. Please try again.");
                                break;
                            case error.UNKNOWN_ERROR:
                            default:
                                alert("An error occurred while accessing your location.");
                                break;
                        }
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
            
            // Remove loading overlay
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                setTimeout(function() {
                    loadingOverlay.style.opacity = 0;
                    setTimeout(function() {
                        loadingOverlay.style.display = 'none';
                    }, 300);
                }, 500);
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
  </body>
</html>
