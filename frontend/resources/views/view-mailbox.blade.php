<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Mailbox Dashboard</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <link href="{{ asset('css/mailbox-style.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
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
                    $unreadMessages = 0;
                    foreach ($broadcasts as $broadcast) {
                        $hasRead = false;
                        if (isset($broadcast['responses'])) {
                            foreach ($broadcast['responses'] as $response) {
                                if (isset($response['userId']) && $response['userId'] == Auth::id()) {
                                    $hasRead = true;
                                    break;
                                }
                            }
                        }
                        if (!$hasRead) {
                            $unreadMessages++;
                        }
                    }
                @endphp
                    
                    {{-- <button type="submit" class="btn btn-link position-relative p-0">
                        <i class="fas fa-envelope text-white" style="font-size: 1.2rem;"></i>
                        @if($unreadMessages > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadMessages }}
                            </span>
                        @endif
                    </button> --}}
                </form>
                
                <!-- Updated HTML Structure -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item custom-dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item custom-dropdown-item" style="width: 100%; text-align: left; background: none; border: none; display: flex; align-items: center;">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>


    <div class="container main-container">
        <div class="main-card">
            @if(session()->has("success"))
                <div class="alert alert-success">
                    {{ session()->get("success") }}
                </div>
            @endif
            @if(session()->has("error"))
                <div class="alert alert-error">
                    {{ session()->get("error") }}
                </div>
            @endif
            
            @if($unreadMessages > 0)
                <div class="alert alert-success">
                    <i class="fas fa-bell me-2"></i> Welcome back! You have {{ $unreadMessages }} unread message{{ $unreadMessages > 1 ? 's' : '' }}.
                </div>
            @endif
            
            <h2 class="mailbox-text-header">
                <i class="fas fa-inbox me-2"></i> Your Mailbox
            </h2>
            
            <table class="table mail-table">
                <thead>
                    <tr>
                        <th style="width: 60%">Message</th>
                        <th style="width: 20%">Date</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($broadcasts as $broadcast)
                        @php
                            // Check if the current user has responded to this broadcast
                            $hasUserResponded = false;
                            if (isset($broadcast['responses'])) {
                                foreach ($broadcast['responses'] as $response) {
                                    if (isset($response['userId']) && $response['userId'] == Auth::id()) {
                                        $hasUserResponded = true;
                                        break;
                                    }
                                    if (isset($response['user_id']) && $response['user_id'] == Auth::id()) {
                                        $hasUserResponded = true;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <tr class="{{ $hasUserResponded ? 'read-row' : 'unread-row' }}" onclick="window.location='{{ route('broadcast.view', $broadcast['id']) }}'">
                            <td class="message-content">
                                @if(!$hasUserResponded)
                                    <span class="badge bg-primary me-1">New</span>
                                @endif
                                {{ $broadcast['message'] }}
                            </td>
                            <td class="message-date">{{ $broadcast['start_off_date'] }}</td>
                            <td>
                                <a href="{{ route('broadcast.view', $broadcast['id']) }}" class="view-button">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    {{-- <script>
        // This script would be connected to your backend logic
        // Example of a function that could mark messages as read when clicked
        document.querySelectorAll('.mail-table tbody tr').forEach(row => {
            row.addEventListener('click', function() {
                this.classList.remove('unread-row');
                this.classList.add('read-row');
                
                // Update the unread count in the notification badge
                const badge = document.querySelector('.notification-badge');
                let count = parseInt(badge.textContent);
                if (count > 0) {
                    count--;
                    badge.textContent = count;
                    if (count === 0) {
                        badge.style.display = 'none';
                    }
                }
                
                // Here you would typically make an AJAX call to mark the message as read in your backend
            });
        });
    </script>   --}}
</body>
</html>
