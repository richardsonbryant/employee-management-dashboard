<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Broadcast</title>
    <link href="{{ asset('css/approval-style.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <div class="navbar-left">
                <a class="navbar-brand" href="{{ Auth::user()->role === 'admin' ? route('admin-dashboard') : route('employee-dashboard') }}">
                    My Dashboard
                </a>
            </div>
            <div class="navbar-right">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" style="font-size: 20px">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card mt-4">

        @if(session()->has("success"))
            <div class="alert alert-success">
                {{ session()->get("success") }}
            </div>
        @endif

        <div class="card-body">
            <div class="d-flex justify-content-center align-items-center mb-3 position-relative">
                <a href="{{ route('mailbox') }}" class="back-button-mailbox position-absolute start-0">←</a>
                <h2 class = 'heading-text mb-4'style ="text-align: center;">Cuti Bersama</h2>
            </div>
            <div class="mb-3">
                <label for="start_off_date" class="form-label">Tanggal Mulai Cuti Bersama</label>
                <input type="text" class="form-control" id="start_off_date" name="StartOffDate" value="{{ $broadcast['start_off_date'] }}" readonly>
            </div>
            <div class="mb-3">
                <label for="end_off_date" class="form-label">Tanggal Berakhir Cuti Bersama </label>
                <input type="text" class="form-control" id="end_off_date" name="end_off_date" value="{{ $broadcast['end_off_date'] }}" readonly>
            </div>
            <div class="mb-3">
                <label for="total_off_day" class="form-label">Total Hari Cuti Bersama</label>
                <input type="text" class="form-control" id="total_off_day" name="total_off_day" value="{{ $broadcast['total_off_day'] }}" readonly>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Pesan</label>
                <input type="text" class="form-control mb-4" id="message" name="message" value="{{ $broadcast['message'] }}" readonly>
            </div>
            @php
            // Check if the user has already responded to this broadcast
            $response = collect($broadcast['responses'])->firstWhere('userId', Auth::id());
        @endphp
        
        @if($response)
            <p><strong>Status:</strong> {{ ucfirst($response['response']) }}</p>
        @else
            <form action="{{ route('broadcast.accept', $broadcast['id']) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success mb-2">Accept</button>
            </form>
            <form action="{{ route('broadcast.reject', $broadcast['id']) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Reject</button>
            </form>
        @endif
        </div>
        </div>
        </div>
    </div>
</body>
</html>