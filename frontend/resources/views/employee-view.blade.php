<!-- resources/views/approval_sick.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Employee Request</title>
    <link href="{{ asset('css/approval-style.css') }}" rel="stylesheet">
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
    <div class="container">
        <div class="card mt-4">
            <div class="d-flex justify-content-center align-items-center mb-3 position-relative">
                <a 
                href="{{ match(request()->query('from')) {
                  'employee-dashboard' => route('employee-dashboard', ['email' => $user['email']]),
                  'view-history' => route('viewHistoryData', ['email' => $user['email']])
                } }}" 
                class="back-button position-absolute start-0"
              >
                ←
              </a>

                <h2 class="heading-text text-center">Izin Sakit</h2>
            </div>

            @if(session()->has("success"))
                <div class="alert alert-success">{{ session()->get("success") }}</div>
            @endif
            @if(session()->has("error"))
                <div class="alert alert-danger">{{ session()->get("error") }}</div>
            @endif

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('loadEmployeeRequest', ['id' => $user['id'], 'type' => $type]) }}" method="GET">

                    @csrf

                    <!-- Tambahkan hidden input untuk tracking tipe -->
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="mb-3">
                        <label for="new_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="new_name" name="new_name" value="{{ $user['new_name'] }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="start_off_date" class="form-label">Tanggal Mulai Cuti</label>
                        <input type="text" class="form-control" id="start_off_date" name="start_off_date" value="{{ $user['start_off_date'] }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="end_off_date" class="form-label">Tanggal Berakhir Cuti</label>
                        <input type="text" class="form-control" id="end_off_date" name="end_off_date" value="{{ $user['end_off_date'] }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="total_off_day" class="form-label">Total Hari Cuti</label>
                        <input type="number" class="form-control" id="total_off_day" name="total_off_day" value="{{ $user['total_off_day'] }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan</label>
                        <input type="text" class="form-control" id="reason" name="reason" value="{{ $user['reason'] }}" readonly>
                    </div>

                    
                    @if ($type == 'sick')
                    <div class="mb-3">
                        <label for="permission_letter" class="form-label">Surat Izin</label>
                        <input type="text" class="form-control" id="permission_letter" name="permission_letter" value="{{ $user['permission_letter'] }}" readonly>
            
                        @if ($user['permission_letter'])
                            <div class="mt-2">
                                <a href="{{ $user['permission_letter'] }}" target="_blank" class="btn btn-primary">
                                    Lihat Surat Izin
                                </a>
                            </div>
                            @endif
                        @endif
                    {{-- @if($user->approval_status == 'pending')
                    <div class="mb-3">
                        <label for="has_doctor_letter" class="form-label">Memiliki Surat Dokter</label>
                        <select class="form-select" id="has_doctor_letter" name="has_doctor_letter">
                            <option value="1" {{ $user->has_doctor_letter ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ !$user->has_doctor_letter ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                
                    <!-- Tombol Approve & Reject -->
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-sm mb-2">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>

                    @elseif($user->approval_status == 'approved' | $user->approval_status == 'rejected')

                    <div class="mb-3">
                        <label for="has_doctor_letter" class="form-label">Memiliki Surat Dokter</label>
                        <input type="text" class="form-control" id="has_doctor_letter" name="has_doctor_letter" 
                            value="{{ $user->has_doctor_letter == 1 ? 'Have Doctor Permission' : 'Not Have Doctor Permission' }}" readonly>
                    </div> --}}

                    <div class="mb-3">
                        <label for="approvalStatus" class="form-label">Approval Status</label>
                        <input type="text" class="form-control" id="approvalStatus" name="approvalStatus" value="{{ $user['approval_status'] }}" readonly>
                    </div>
                    
                
                </form>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>