<!-- resources/views/approval_sick.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approval Sick</title>
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
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ request()->query('from') === 'view-user' ? route('viewUserData', ['email' => $user['email']]) : route('admin-dashboard') }}" class="btn-back">
              <i class="fas fa-arrow-left"></i> Back
            </a>
            <h5 class="mb-0 fw-bold">Izin Sakit</h5>
            
            @if($user['approval_status'] == 'pending')
              <span class="status-badge pending">
                <i class="fas fa-clock me-1"></i> Pending
              </span>
            @elseif($user['approval_status'] == 'approved')
              <span class="status-badge approved">
                <i class="fas fa-check-circle me-1"></i> Approved
              </span>
            @elseif($user['approval_status'] == 'rejected')
              <span class="status-badge rejected">
                <i class="fas fa-times-circle me-1"></i> Rejected
              </span>
            @endif
          </div>
          
          <div class="card-body">
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
            
            @if ($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            
            <form action="{{ route('handleSickData.post', ['id' => $user['id']]) }}" method="POST">
              @csrf
              
              <div class="user-info">
                <div class="mb-3">
                  <label for="new_name" class="form-label">Nama</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="new_name" name="new_name" value="{{ $user['new_name'] }}" readonly>
                  </div>
                </div>
              </div>
              
              <div class="leave-info">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="start_off_date" class="form-label">Tanggal Mulai Sakit</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                        <input type="text" class="form-control" id="start_off_date" name="start_off_date" value="{{ $user['start_off_date'] }}" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="end_off_date" class="form-label">Tanggal Berakhir Sakit</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                        <input type="text" class="form-control" id="end_off_date" name="end_off_date" value="{{ $user['end_off_date'] }}" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="total_off_day" class="form-label">Total Hari Sakit</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                    <input type="number" class="form-control" id="total_off_day" name="total_off_day" value="{{ $user['total_off_day'] }}" readonly>
                  </div>
                </div>
              </div>
              
              <div class="leave-reason mb-4">
                <label for="reason" class="form-label">Alasan</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-comment-alt"></i></span>
                  <textarea class="form-control" id="reason" name="reason" rows="3" readonly>{{ $user['reason'] }}</textarea>
                </div>
              </div>
              
              <div class="medical-documents mb-4">
                <div class="mb-3">
                  <label for="permission_letter" class="form-label">Surat Izin</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                    <input type="text" class="form-control" id="permission_letter" name="permission_letter" value="{{ $user['permission_letter'] }}" readonly>
                  </div>
                  
                  @if ($user['permission_letter'])
                    <div class="mt-2 text-end">
                      <a href="{{ $user['permission_letter'] }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i> Lihat Surat Izin
                      </a>
                    </div>
                  @endif
                </div>
                
                @if($user['approval_status'] == 'pending')
                  <div class="mb-3">
                    <label for="has_doctor_letter" class="form-label">Memiliki Surat Dokter</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
                      <select class="form-select" id="has_doctor_letter" name="has_doctor_letter">
                        <option value="1" {{ $user['has_doctor_letter'] ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ !$user['has_doctor_letter'] ? 'selected' : '' }}>Tidak</option>
                      </select>
                    </div>
                  </div>
                @else
                  <div class="mb-3">
                    <label for="has_doctor_letter" class="form-label">Memiliki Surat Dokter</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
                      <input type="text" class="form-control" id="has_doctor_letter" name="has_doctor_letter" 
                        value="{{ $user['has_doctor_letter'] == 1 ? 'Ya' : 'Tidak' }}" readonly>
                    </div>
                  </div>
                @endif
              </div>
              
              @if($user['approval_status'] == 'pending')
                <div class="action-buttons">
                  <button type="submit" name="action" value="approve" class="btn btn-action btn-approve flex-grow-1">
                    <i class="fas fa-check me-2"></i> Approve
                  </button>
                  <button type="submit" name="action" value="reject" class="btn btn-action btn-reject flex-grow-1">
                    <i class="fas fa-times me-2"></i> Reject
                  </button>
                </div>
              @endif
            </form>
          </div>
        </div>
      </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>