<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Broadcast</title>
    <link href="{{ asset('css/form-style.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="container mt-5">
        <div class="card">
        <h2 style ="text-align: center;">Send Broadcast</h2>
        @if(session()->has("success"))
            <div class="alert alert-success">32
                {{ session()->get("success") }}
            </div>
        @endif
        <div class="card-body">
        <form action="{{ route('broadcast.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="start_off_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_off_date" name="start_off_date" required>
            </div>
            <div class="mb-3">
                <label for="end_off_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_off_date" name="end_off_date" required>
            </div>
            <div class="mb-3">
                <label for="total_off_day" class="form-label">Total Off Day</label>
                <input type="number" class="form-control" id="total_off_day" name="total_off_day" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Broadcast</button>
        </form>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>