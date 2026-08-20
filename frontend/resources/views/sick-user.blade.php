<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sick Permission</title>
    <link href="{{ asset('css/form-style.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <style>
</style>
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
  <!-- Improved Container -->
  <div class="container">
    <div class="card">
        <!-- Card Header with Back Button and Title -->
        <div class="heading-wrapper">
            <a href="{{ Auth::user()->role === 'admin' ? route('admin-dashboard') : route('employee-dashboard') }}" class="back-button">
                <i class="fas fa-chevron-left"></i> <span class="ms-1">Kembali</span>
            </a>
            <h2 class="card-title mx-auto">Izin Sakit</h2>
        </div>
        
        <!-- Alert Messages -->
        @if(session()->has("success"))
        <div class="alert alert-success mx-4 mt-4">
            <i class="fas fa-check-circle me-2"></i> {{session()->get("success")}}
        </div>
        @endif
        
        @if(session()->has("error"))
        <div class="alert alert-danger mx-4 mt-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{session()->get("error")}}
        </div>
        @endif
        
        <!-- Form Body -->
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form action="{{route('addSickUser.post')}}" method="post">
                @csrf
                <div class="mb-4">
                    <label for="new_name" class="form-label required-field">Nama</label>
                    <input type="text" class="form-control" id="new_name" name="new_name" required placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-group-date mb-4">
                    <div>
                        <label for="start_off_date" class="form-label required-field">Tanggal Mulai Sakit</label>
                        <input type="date" class="form-control" id="start_off_date" name="start_off_date" required>
                        <div class="form-text">Tanggal pertama izin sakit</div>
                    </div>
                    <div>
                        <label for="end_off_date" class="form-label required-field">Tanggal Berakhir Sakit</label>
                        <input type="date" class="form-control" id="end_off_date" name="end_off_date" required>
                        <div class="form-text">Tanggal terakhir izin sakit</div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label required-field">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email anda">
                </div>
                
                <div class="mb-4">
                    <label for="reason" class="form-label required-field">Alasan Sakit</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Jelaskan kondisi kesehatan anda secara detail"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="permission_letter" class="form-label">Surat Izin Dokter</label>
                    <input type="text" class="form-control" id="permission_letter" name="permission_letter" placeholder="Link Surat Izin Dokter">
                    <div class="form-text">Masukkan link dokumen surat keterangan dari dokter</div>
                </div>
                
                <div class="form-footer">
                    <button type="reset" class="btn btn-outline-secondary" style="width: auto; margin-right: 10px;">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary" style="width: auto;">
                        <i class="fas fa-paper-plane me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  </body>
</html>