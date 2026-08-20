<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserData;
use App\Models\WfhData;
use App\Models\PermissionData;
use App\Models\Broadcast;
use App\Models\BroadcastResponse;
use App\Models\UserAttendance;
use Illuminate\Http\Request;
use App\Helpers\GoogleCalendar;
use \Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use App\Services\UserDataAPIService;
use App\Services\WfhDataAPIService;
use App\Services\PermissionDataAPIService;
use App\Services\BroadcastAPIService;
use App\Services\BroadcastResponseAPIService;
use App\Services\UserAttendanceAPIService;
use App\Services\UserAPIService;

class UserController extends Controller
{
    protected $userDataService;
    protected $wfhDataService;
    protected $permissionDataService;
    protected $broadcastService;
    protected $broadcastResponseService;
    protected $userAttendanceService;
    protected $userService;


    public function __construct(
        UserDataAPIService $userDataService,
        WfhDataAPIService $wfhDataService,
        PermissionDataAPIService $permissionDataService,
        BroadcastAPIService $broadcastService,
        BroadcastResponseAPIService $broadcastResponseService,
        UserAttendanceAPIService $userAttendanceService,
        UserAPIService $userService
    ) {
        $this->userDataService = $userDataService;
        $this->wfhDataService = $wfhDataService;
        $this->permissionDataService = $permissionDataService;
        $this->broadcastService = $broadcastService;
        $this->broadcastResponseService = $broadcastResponseService;
        $this->userAttendanceService = $userAttendanceService;
        $this->userService = $userService;
    }

    public function loadAllUsers()
    {
        try {
            // Ambil data dari 3 API berbeda
            $userData = collect($this->userDataService->getAllUserData())
                ->map(function ($item) {
                    $item = (object) $item;
                    $item->source = 'user';
                    return $item;
                });

            $wfhData = collect($this->wfhDataService->getAllWfhData())
                ->map(function ($item) {
                    $item = (object) $item;
                    $item->source = 'wfh';
                    return $item;
                });

            $permissionData = collect($this->permissionDataService->getAllPermissionData())
                ->map(function ($item) {
                    $item = (object) $item;
                    $item->source = 'permission';
                    return $item;
                });
            // Gabungkan semua data
            $allData = $userData->merge($wfhData)->merge($permissionData);

            // Ambil status dari query string
            $status = request()->query('status');

            // Jika status valid, filter berdasarkan approval_status
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $allData = $allData->filter(function ($item) use ($status) {
                    return $item->approval_status === $status;
                });
            }

            // Urutkan data berdasarkan waktu pembuatan
            $allData = $allData->sortByDesc(function ($item) {
                return \Carbon\Carbon::parse($item->createdAt);
            });

            // Pagination setup
            $perPage = 10;
            $currentPage = request()->input('page', 1);
            $pagedData = $allData->forPage($currentPage, $perPage);

            $allDataPaginated = new LengthAwarePaginator(
                $pagedData,
                $allData->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $allDataPaginated->onEachSide(1);

            // Ambil data cuti bersama via Google Calendar API (pakai helper yang sudah ada)
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
            $cutiBersamaDates = GoogleCalendar::getCutiBersamaDates($month, $year);

            return view('admin-permission', [
                'all_user' => $allDataPaginated,
                'cutiBersamaEvents' => $cutiBersamaDates,
                'currentDate' => Carbon::now()->format('d-m-Y'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data from APIs: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data dari API. Coba lagi nanti.');
        }
    }
    // public function loadUserDashboard()
    // {
    //     $user = session('user'); // Ambil data user dari session
    //     $today = Carbon::today();

    //     // Mengakses email user dengan notasi array
    //     $userFromApi = $this->userService->getUserByEmail($user['email']);

    //     // Ambil data dari API (pastikan data sudah dalam format array)
    //     $userData = collect($this->userDataService->getAllUserData());
    //     $wfhData = collect($this->wfhDataService->getAllWfhData());
    //     $permissionData = collect($this->permissionDataService->getAllPermissionData());
    //     $broadcasts = collect($this->broadcastService->getAllBroadcasts())->map(function ($broadcast) {
    //         $broadcast['responses'] = collect($broadcast['responses'] ?? [])->map(function ($response) {
    //             return [
    //                 'user_id' => $response['userId'],
    //                 'response' => $response['response'],
    //             ];
    //         });
    //         return $broadcast;
    //     });

    //     // Filter berdasarkan email user
    //     $filteredUserData = $userData->where('email', $user['email']);
    //     $filteredWfhData = $wfhData->where('email', $user['email']);
    //     $filteredPermissionData = $permissionData->where('email', $user['email']);

    //     // Gabungkan semua request user
    //     $userRequests = collect()
    //         ->merge($filteredUserData)
    //         ->merge($filteredWfhData)
    //         ->merge($filteredPermissionData);

    //     // Sortir berdasarkan created_at (jika tersedia)
    //     $userRequests = $userRequests->map(function ($item) {
    //         $item['createdAt'] = Carbon::parse($item['createdAt']);
    //         return $item;
    //     })->sortByDesc('createdAt');

    //     // Pagination manual
    //     $perPage = 10;
    //     $currentPage = request()->input('page', 1);
    //     $pagedData = $userRequests->forPage($currentPage, $perPage);
    //     $allDataPaginated = new LengthAwarePaginator($pagedData, $userRequests->count(), $perPage, $currentPage, [
    //         'path' => request()->url(),
    //         'query' => request()->query(),
    //     ]);

    //     // Attendance hari ini
    //     $allAttendance = collect($this->userAttendanceService->getAllUserAttendances());

    //     $attendanceToday = $allAttendance->first(function ($item) use ($user, $today) {
    //         return $item['email'] == $user['email'] && Carbon::parse($item['clock_in'])->isSameDay($today);
    //     });

    //     // Periksa jika data attendance tersedia dan mengakses dengan notasi array
    //     $clockedIn = $attendanceToday != null;
    //     $clockedOut = $attendanceToday && !empty($attendanceToday['clock_out']);

    //     // dd($userFromApi);
    //     return view("employee-dashboard", [
    //         'leave_quota' => $userFromApi['leaveQuota'] ?? 0,
    //         'user_data' => $allDataPaginated,
    //         'currentDate' => Carbon::now()->format('d-m-Y'),
    //         'broadcasts' => $broadcasts,
    //         'clockedIn' => $clockedIn,
    //         'clockedOut' => $clockedOut,
    //         'userRequests' => $userRequests  // Mengirimkan data yang diformat
    //     ]);
    // }

    public function loadUserDashboard()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $userFromApi = $this->userService->getUserByEmail($user->email);

        // Ambil data dari API (pastikan data sudah dalam format array)
        $userData = collect($this->userDataService->getAllUserData());  // Menganggap data sudah dalam bentuk array
        $wfhData = collect($this->wfhDataService->getAllWfhData());
        $permissionData = collect($this->permissionDataService->getAllPermissionData());
        $broadcasts = collect($this->broadcastService->getAllBroadcasts())->map(function ($broadcast) {
            $broadcast['responses'] = collect($broadcast['responses'] ?? [])->map(function ($response) {
                return [
                    'user_id' => $response['userId'],
                    'response' => $response['response'],
                ];
            });
            return $broadcast;
        });

        // Filter berdasarkan email user
        $filteredUserData = $userData->where('email', $user->email);
        $filteredWfhData = $wfhData->where('email', $user->email);
        $filteredPermissionData = $permissionData->where('email', $user->email);

        // Gabungkan semua request user
        $userRequests = collect()
            ->merge($filteredUserData)
            ->merge($filteredWfhData)
            ->merge($filteredPermissionData);

        // Ambil status dari query string
        $status = request()->query('status');

        // Jika status valid, filter berdasarkan approval_status
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $userRequests = $userRequests->filter(function ($item) use ($status) {
                return isset($item['approval_status']) && $item['approval_status'] == $status;
            });
        }

        // Sortir berdasarkan created_at (jika tersedia)
        $userRequests = $userRequests->map(function ($item) {
            $item['createdAt'] = Carbon::parse($item['createdAt']);
            return $item;
        })->sortByDesc('createdAt');

        // Pagination manual
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $pagedData = $userRequests->forPage($currentPage, $perPage);
        $allDataPaginated = new LengthAwarePaginator($pagedData, $userRequests->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        // Attendance hari ini
        $allAttendance = collect($this->userAttendanceService->getAllUserAttendances());

        $attendanceToday = $allAttendance->first(function ($item) use ($user, $today) {
            return $item['email'] == $user['email'] && Carbon::parse($item['clock_in'])->isSameDay($today);
        });


        // Periksa jika data attendance tersedia dan mengakses dengan notasi array
        $clockedIn = $attendanceToday != null;
        $clockedOut = $attendanceToday && !empty($attendanceToday['clock_out']);

        // dd($userFromApi);
        return view("employee-dashboard", [
            'leave_quota' => $userFromApi['leaveQuota'] ?? 0,
            'user_data' => $allDataPaginated,
            'currentDate' => Carbon::now()->format('d-m-Y'),
            'broadcasts' => $broadcasts,
            'clockedIn' => $clockedIn,
            'clockedOut' => $clockedOut,
            'userRequests' => $userRequests  // Mengirimkan data yang diformat
        ]);
    }

    public function loadSummaryEmployee(Request $request)
    {
        $search = $request->input('search');

        // Ambil semua data user dari API
        $users = collect($this->userService->getAllUsers());

        // Filter hanya yang role-nya 'employee'
        $employee = $users->filter(function ($user) {
            return isset($user['role']) && $user['role'] === 'employee';
        });

        // Kalau ada search, filter berdasarkan nama
        if ($search) {
            $employee = $employee->filter(function ($user) use ($search) {
                return stripos($user['name'], $search) !== false;
            });
        }

        $employee = $employee->sortBy('name')->values();

        return view('summary', [
            'employee' => $employee,
            'search' => $search,
        ]);
    }


    public function loadSummaryAttendance(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Ambil semua user dari API
        $users = collect($this->userService->getAllUsers());

        // Filter hanya role employee
        $employees = $users->filter(function ($user) {
            return isset($user['role']) && $user['role'] === 'employee';
        });

        // Ambil semua attendance dari API
        $attendances = collect($this->userAttendanceService->getAllUserAttendances());

        // Filter attendance berdasarkan tanggal
        $filteredAttendances = $attendances->filter(function ($attendance) use ($date) {
            return \Carbon\Carbon::parse($attendance['clock_in'])->toDateString() === $date;
        });

        // Gabungkan users dengan attendances
        $usersWithAttendance = $employees->map(function ($user) use ($filteredAttendances) {
            $user['attendances'] = $filteredAttendances->filter(function ($attendance) use ($user) {
                return $attendance['email'] === $user['email'];
            })->values(); // biar indexing ulang
            return $user;
        })->sortBy('name')->values();

        return view('attendance-summary', [
            'users' => $usersWithAttendance,
            'date' => $date,
        ]);
    }


    public function viewUserData($email)
    {
        // Ambil data user dari API
        $user = $this->userService->getUserByEmail($email); // Asumsi sudah ada

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        // Ambil parameter filter dari request
        $year = request('year');
        $month = request('month');
        $requestType = request('requestType');
        $page = request('page', 1);
        $perPage = 10;

        // Ambil data cuti tahunan
        $annualLeaveResponse = $this->userDataService->getUserRequests($email, $year, $month, $requestType, $page, $perPage);
        $annualLeaves = $this->paginateApiData($annualLeaveResponse, $page, $perPage);

        // Ambil data WFH
        $wfhResponse = $this->wfhDataService->getWfhRequests($email, $year, $month, $requestType, $page, $perPage);
        $wfhRequests = $this->paginateApiData($wfhResponse, $page, $perPage);

        // Ambil data cuti sakit
        $sickResponse = $this->permissionDataService->getSickLeaveRequests($email, $year, $month, $requestType, $page, $perPage);
        $sickRequests = $this->paginateApiData($sickResponse, $page, $perPage);

        // dd($annualLeaves, $wfhRequests, $sickRequests);

        // Return view dengan data
        return view('view-user', compact('user', 'annualLeaves', 'wfhRequests', 'sickRequests'));
    }

    // Fungsi helper untuk konversi data API menjadi LengthAwarePaginator
    private function paginateApiData($response, $page, $perPage)
    {
        if (isset($response['error'])) {
            return new LengthAwarePaginator([], 0, $perPage, $page);
        }

        $data = $response['data'] ?? [];
        $total = $response['total'] ?? count($data);
        $currentPage = $response['currentPage'] ?? $page;

        return new LengthAwarePaginator(
            collect($data),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function exportAttendance(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Ambil semua data dari API
        $allAttendance = $this->userAttendanceService->getAllUserAttendances();

        // Filter berdasarkan tanggal clock_in
        $filtered = collect($allAttendance)->filter(function ($item) use ($date) {
            return isset($item['clock_in']) && substr($item['clock_in'], 0, 10) === $date;
        })->values();

        // Format data agar memiliki kunci 'attendances'
        $users = $filtered->map(function ($attendance) {
            return [
                'name' => $attendance['name'],
                'attendances' => [$attendance], // Membungkus absensi dalam array 'attendances'
            ];
        });

        // Load view untuk PDF
        $pdf = PDF::loadView('export-attendance-pdf', [
            'users' => $users,
            'date' => $date
        ]);

        return $pdf->download('Attendance_' . $date . '.pdf');
    }


    public function exportUserData($email)
    {
        $user = collect($this->userService->getAllUsers())
            ->firstWhere('email', $email);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        $year = request('year');
        $month = request('month');
        $requestType = request('requestType');

        $filterFunc = function ($item) use ($email, $year, $month) {
            return $item['email'] == $email &&
                (!$year || substr($item['start_off_date'], 0, 4) == $year) &&
                (!$month || substr($item['start_off_date'], 5, 2) == $month);
        };

        $annualLeaves = collect($this->userDataService->getAllUserData())
            ->filter($filterFunc)
            ->values()->toArray();

        $wfhRequests = collect($this->wfhDataService->getAllWfhData())
            ->filter($filterFunc)
            ->values()->toArray();

        $sickRequests = collect($this->permissionDataService->getAllPermissionData())
            ->filter($filterFunc)
            ->values()->toArray();


        // PDF
        $pdf = Pdf::loadView('export-user-pdf', [
            'user' => $user,
            'annualLeaves' => ($requestType === 'annual' || !$requestType) ? $annualLeaves : [],
            'wfhRequests' => ($requestType === 'wfh' || !$requestType) ? $wfhRequests : [],
            'sickRequests' => ($requestType === 'sick' || !$requestType) ? $sickRequests : [],
            'year' => $year,
            'month' => $month,
            'requestType' => $requestType,
        ]);

        return $pdf->download("UserData_{$user['name']}.pdf");
    }

    public function viewHistoryData($email)
    {
        // Fetch user data based on email
        $user = $this->userService->getUserByEmail($email);
        // $user = $userResponse['data'] ?? null;

        // dd($userResponse);
        // if (!$user) {
        //     return redirect()->back()->with('error', 'User not found');
        // }

        // Ambil parameter filter dari request
        $year = request('year');
        $month = request('month');
        $requestType = request('requestType');
        $page = request('page', 1);
        $perPage = 10;

        // Ambil data cuti tahunan
        $annualLeaveResponse = $this->userDataService->getUserRequests($email, $year, $month, $requestType, $page, $perPage);
        $annualLeaves = $this->paginateApiData($annualLeaveResponse, $page, $perPage);

        // Ambil data WFH
        $wfhResponse = $this->wfhDataService->getWfhRequests($email, $year, $month, $requestType, $page, $perPage);
        $wfhRequests = $this->paginateApiData($wfhResponse, $page, $perPage);

        // Ambil data cuti sakit
        $sickResponse = $this->permissionDataService->getSickLeaveRequests($email, $year, $month, $requestType, $page, $perPage);
        $sickRequests = $this->paginateApiData($sickResponse, $page, $perPage);

        // dd($annualLeaves, $wfhRequests, $sickRequests);

        // Return view dengan data

        return view('view-history', compact('user', 'annualLeaves', 'wfhRequests', 'sickRequests'));
    }


    public function AddUserForm()
    {
        return view("add-user");
    }

    public function AddWfhUserForm()
    {
        return view("wfh-user");
    }

    public function AddSickUserForm()
    {
        return view("sick-user");
    }

    public function AddNoPermissionUserForm()
    {
        return view('add-no-permission-user');
    }

    public function addUser(Request $request, UserDataApiService $userDataApiService)
    {
        $request->validate([
            'new_name' => ['required', 'max:255'],
            'start_off_date' => ['required', 'date', 'after:' . now()->addDays(15)->toDateString()],
            'end_off_date' => 'required|date|after_or_equal:start_off_date',
            'reason' => 'required|string',
        ], [
            'end_off_date.after_or_equal' => 'End Off Date must be the same or greater than Start Off Date.',
        ]);

        $total_off_day = GoogleCalendar::countLeaveDays($request->start_off_date, $request->end_off_date);

        $user = Auth::user(); // ambil user yg login

        $data = [
            'new_name' => $request->new_name,
            'email' => $request->email,
            'start_off_date' => $request->start_off_date,
            'end_off_date' => $request->end_off_date,
            'total_off_day' => $total_off_day,
            'reason' => $request->reason,
            'request_type' => 'annual',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        // dd($request);

        $response = $userDataApiService->createUserData($data);

        if (isset($response['error'])) {
            return redirect()->route("home")->with("error", "Failed to create request via API");
        }

        if ($user->role == 'admin') {
            return redirect(route('admin-dashboard'))
                ->with("success", 'Request has been created successfully via API');
        } elseif ($user->role == 'employee') {
            return redirect(route('employee-dashboard'))
                ->with("success", 'Request has been created successfully via API');
        }

        return redirect(route("home"))
            ->with("success", "Request has been created successfully via API");
    }

    public function addNoPermissionUser(Request $request, UserDataApiService $userDataApiService)
    {
        $request->validate([
            'new_name' => ['required', 'max:255'],
            'email' => ['required', 'email'],
            'start_off_date' => ['required', 'date'],
            'end_off_date' => ['required', 'date', 'after_or_equal:start_off_date'],
            'reason' => ['required', 'string'],
        ], [
            'end_off_date.after_or_equal' => 'End Off Date must be the same or greater than Start Off Date.',
        ]);

        // Hitung total hari cuti (tidak termasuk weekend dan tanggal merah)
        $total_off_day = GoogleCalendar::countLeaveDays($request->start_off_date, $request->end_off_date);

        // Siapkan data untuk dikirim ke API
        $data = [
            'new_name' => $request->new_name,
            'email' => $request->email,
            'start_off_date' => $request->start_off_date,
            'end_off_date' => $request->end_off_date,
            'total_off_day' => $total_off_day,
            'reason' => $request->reason,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Kirim data ke API
        $result = $userDataApiService->createUserData($data);

        if ($result) {
            $user = Auth::user();

            if ($user->role == 'admin') {
                return redirect(route('admin-dashboard'))
                    ->with("success", 'Request has been created successfully');
            } elseif ($user->role == 'employee') {
                return redirect(route('employee-dashboard'))
                    ->with("success", 'Request has been created successfully');
            }
        }

        return redirect(route("home"))->with("error", "Failed to create request");
    }
    public function addWfhUser(Request $request, WfhDataApiService $wfhApi)
    {
        $request->validate([
            'new_name' => ['required', 'max:255'],
            'start_off_date' => ['required', 'date'],
            'end_off_date' => 'required|date|after_or_equal:start_off_date',
            'reason' => 'required|string',
        ], [
            'end_off_date.after_or_equal' => 'End Off Date must be the same or greater than Start Off Date.',
        ]);

        $total_off_day = \App\Helpers\GoogleCalendar::countLeaveDays($request->start_off_date, $request->end_off_date);

        // Siapkan data untuk dikirim ke API Spring Boot
        $data = [
            'new_name' => $request->new_name,
            'email' => $request->email,
            'start_off_date' => $request->start_off_date,
            'end_off_date' => $request->end_off_date,
            'total_off_day' => $total_off_day,
            'reason' => $request->reason,
            'request_type' => 'wfh',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $wfhApi->createWfhData($data);

        if ($response) {
            $user = Auth::user();
            if ($user->role == 'admin') {
                return redirect(route('admin-dashboard'))
                    ->with("success", 'Request has been created successfully via API');
            } elseif ($user->role == 'employee') {
                return redirect(route('employee-dashboard'))
                    ->with("success", 'Request has been created successfully via API');
            }
        }

        return redirect(route("home"))
            ->with("error", "Failed to create request via API");
    }
    public function addSickUser(Request $request, PermissionDataApiService $permissionApi)
    {
        $request->validate([
            'new_name' => ['required', 'max:255'],
            'start_off_date' => ['required', 'date'],
            'end_off_date' => 'required|date|after_or_equal:start_off_date',
            'reason' => 'required|string',
        ], [
            'end_off_date.after_or_equal' => 'End Off Date must be the same or greater than Start Off Date.',
        ]);

        $total_off_day = \App\Helpers\GoogleCalendar::countLeaveDays($request->start_off_date, $request->end_off_date);

        $data = [
            'new_name' => $request->new_name,
            'email' => $request->email,
            'start_off_date' => $request->start_off_date,
            'end_off_date' => $request->end_off_date,
            'total_off_day' => $total_off_day,
            'reason' => $request->reason,
            'permission_letter' => $request->permission_letter ?? null,
            'request_type' => 'sick',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $permissionApi->createPermissionData($data);

        if ($response) {
            $user = Auth::user();
            if ($user->role == 'admin') {
                return redirect(route('admin-dashboard'))
                    ->with("success", 'Sick leave request created successfully via API');
            } elseif ($user->role == 'employee') {
                return redirect(route('employee-dashboard'))
                    ->with("success", 'Sick leave request created successfully via API');
            }
        }

        return redirect(route("home"))
            ->with("error", "Failed to create sick leave request via API");
    }

    public function loadEmployeeRequest(Request $request, $id)
    {
        $type = $request->query('type'); // misalnya ?type=permission
        $data = null;

        switch ($type) {
            case 'sick':
                $data = collect($this->permissionDataService->getAllPermissionData())->firstWhere('id', $id);
                break;
            case 'wfh':
                $data = collect($this->wfhDataService->getAllWfhData())->firstWhere('id', $id);
                break;
            case 'annual':
                $data = collect($this->userDataService->getAllUserData())->firstWhere('id', $id);
                break;
        }


        //dd($id, $request->query('type'));

        if (!$data) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('employee-view', [
            'user' => $data,
            'type' => $type,
        ]);
    }


    public function loadEditForm($id)
    {
        $user = UserData::find($id);

        return view('approval-user', compact('user'));
    }


    public function loadUserApprovalForm($id)
    {
        // Ambil data dari API, bukan dari model langsung
        $userData = $this->userDataService->getUserDataById($id);

        // Cek jika data tidak ditemukan atau API error
        if (!$userData || isset($userData['error'])) {
            return redirect()->back()->with('error', 'User Data not found or failed to fetch from API');
        }

        return view('approval-user', ['user' => $userData]);
    }


    public function loadWfhApprovalForm($id)
    {
        $wfhData = $this->wfhDataService->getUserDataById($id);

        if (!$wfhData || isset($wfhData['error'])) {
            return redirect()->back()->with('error', 'WFH Data not found or failed to fetch from API');
        }

        return view('approval-wfh-user', ['user' => $wfhData]);
    }
    public function loadSickApprovalForm($id)
    {
        $permissionData = $this->permissionDataService->getUserDataById($id);

        if (!$permissionData || isset($permissionData['error'])) {
            return redirect()->back()->with('error', 'Sick Data not found or failed to fetch from API');
        }

        return view('approval-sick-user', ['user' => $permissionData]);
    }

    public function handleUserData(Request $request, $id)
    {
        // Memanggil API untuk mendapatkan data user
        $userData = $this->userDataService->getUserDataById($id);

        if (!$userData) {
            return redirect()->back()->with('error', 'User Data not found');
        }

        $user = $this->userService->getUserByEmail($userData['email']);

        // dd($user);

        if ($request->input('action') == 'approve') {
            if ($user['leaveQuota'] >= $userData['total_off_day']) {
                // Mengurangi leave quota
                $newLeaveQuota = $user['leaveQuota'] - $userData['total_off_day'];
                $this->userService->updateLeaveQuota($user['id'], $newLeaveQuota);

                // Update approval status
                $this->userDataService->updateApprovalStatus($id, 'approved');

                return redirect()->route('admin-dashboard')->with('success', 'User Data approved and leave quota updated.');
            } else {
                return redirect()->route('admin-dashboard')->with('error', 'Not enough leave quota for approval.');
            }
        } elseif ($request->input('action') == 'reject') {
            // Cuti ditolak, tidak perlu update leave quota
            $this->userDataService->updateApprovalStatus($id, 'rejected');
            return redirect()->route('admin-dashboard')->with('success', 'User Data rejected.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }



    // Method to handle approval and rejection of WfhData
    public function handleWfhData(Request $request, $id)
    {
        // Ambil data WFH via API
        $wfhData = $this->wfhDataService->getUserDataById($id);

        if (!$wfhData) {
            return redirect()->back()->with('error', 'WFH Data not found');
        }

        // Ambil user berdasarkan email
        $user = $this->userService->getUserByEmail($wfhData['email']);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        if ($request->input('action') == 'approve') {
            $this->wfhDataService->updateWfhApprovalStatus($id, 'approved');
            return redirect()->route('admin-dashboard')->with('success', 'WFH Data approved.');
        } elseif ($request->input('action') == 'reject') {
            $newLeaveQuota = $user['leaveQuota'] - $wfhData['total_off_day'];
            $this->userService->updateLeaveQuota($user['id'], $newLeaveQuota);
            $this->wfhDataService->updateWfhApprovalStatus($id, 'rejected');

            return redirect()->route('admin-dashboard')->with('success', 'WFH Data rejected and leave quota updated.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }


    // Method to handle approval and rejection of PermissionData
    public function handleSickData(Request $request, $id)
    {
        // Ambil data sakit via API
        $permissionData = $this->permissionDataService->getUserDataById($id);

        if (!$permissionData) {
            return redirect()->back()->with('error', 'Sick Data not found');
        }

        // Ambil user berdasarkan email
        $user = $this->userService->getUserByEmail($permissionData['email']);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        // Cek apakah sudah melebihi batas tanpa surat dokter
        if ($request->input('has_doctor_letter') == 0) {
            $countWithoutLetter = $this->permissionDataService->getSickLeaveRequests(
                $permissionData['email'],
                now()->year
            )['data'] ?? [];

            $approvedWithoutLetterCount = collect($countWithoutLetter)
                ->where('has_doctor_letter', 0)
                ->where('approval_status', 'approved')
                ->count();

            if ($approvedWithoutLetterCount > 2 && $request->input('action') == 'approve') {
                return redirect()->route('admin-dashboard')->with('error', 'Anda sudah mencapai batas 2 kali approval tanpa surat dokter untuk tahun ini.');
            }
        }

        if ($request->input('action') == 'approve') {
            $this->permissionDataService->updatePermissionApprovalStatus($id, 'approved', $request->input('has_doctor_letter'));
            return redirect()->route('admin-dashboard')->with('success', 'Sick Data approved.');
        } elseif ($request->input('action') == 'reject') {
            $newLeaveQuota = $user['leaveQuota'] - $permissionData['total_off_day'];
            $this->userService->updateLeaveQuota($user['id'], $newLeaveQuota);
            $this->permissionDataService->updatePermissionApprovalStatus($id, 'rejected');

            return redirect()->route('admin-dashboard')->with('success', 'Sick Data rejected and leave quota updated.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function showBroadcastForm()
    {
        return view('broadcast-form');
    }

    public function sendBroadcast(Request $request)
    {
        // Validasi input
        $request->validate([
            'start_off_date' => 'required|date',
            'end_off_date' => 'required|date',
            'total_off_day' => 'required|integer',
            'message' => 'required|string',
        ]);

        // Siapkan data untuk dikirim ke API
        $data = [
            'start_off_date' => $request->start_off_date,
            'end_off_date' => $request->end_off_date,
            'total_off_day' => $request->total_off_day,
            'message' => $request->message,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Kirim ke Spring Boot API
        $result = $this->broadcastService->createBroadcast($data);

        // Cek respon
        if (isset($result['id'])) {
            // Simpan ke session kalau berhasil
            session(['broadcast' => $result]);

            return redirect()->route('admin-dashboard')->with('success', 'Broadcast sent successfully via API!');
        } else {
            return redirect()->back()->with('error', 'Failed to send broadcast via API.');
        }
    }
    public function viewBroadcast($id)
    {
        $broadcast = $this->broadcastService->getBroadcastById($id);

        if (!$broadcast || isset($broadcast['error'])) {
            return redirect()->back()->with('error', 'Broadcast not found');
        }

        return view('view-broadcast', compact('broadcast'));
    }
    public function acceptBroadcast($id)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'User not authenticated');
        }

        $user = Auth::user();

        // Ambil broadcast dari API
        $broadcast = $this->broadcastService->getBroadcastById($id);

        if (!$broadcast || isset($broadcast['error'])) {
            return redirect()->back()->with('error', 'Broadcast not found or API error');
        }

        // Cek apakah user sudah merespon broadcast ini
        $responses = $this->broadcastResponseService->getResponsesByBroadcastId($id);
        $alreadyResponded = collect($responses)->firstWhere('userId', $user->id);

        if ($alreadyResponded) {
            return redirect()->route('employee-dashboard')->with('error', 'You have already responded to this broadcast.');
        }

        // dd($user, $broadcast);

        if ($user->leave_quota >= $broadcast['total_off_day']) {
            // Kurangi jatah cuti user lokal
            $user->leave_quota -= $broadcast['total_off_day'];
            $user->save();

            // Simpan respons via API
            $responseData = [
                'broadcast' => ['id' => $broadcast['id']],
                'userId' => $user->id,
                'response' => 'accepted',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];

            $createdResponse = $this->broadcastResponseService->createBroadcastResponse($responseData);

            if (!$createdResponse) {
                return redirect()->route('employee-dashboard')->with('error', 'Failed to save response to API.');
            }

            // Kirim data ke userData API
            $data = [
                'new_name' => $user->name,
                'email' => $user->email,
                'start_off_date' => $broadcast['start_off_date'],
                'end_off_date' => $broadcast['end_off_date'],
                'total_off_day' => (int) $broadcast['total_off_day'],
                'reason' => $broadcast['message'],
                'request_type' => 'annual',
                'approval_status' => 'approved', // langsung diset approved
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];

            $createdUserData = $this->userDataService->createUserData($data);

            if (isset($createdUserData['error'])) {
                return redirect()->route('employee-dashboard')->with('error', 'Failed to save leave to API: ' . $createdUserData['error']);
            }

            return redirect()->route('employee-dashboard')->with('success', 'Broadcast accepted and leave quota updated.');
        } else {
            return redirect()->route('employee-dashboard')->with('error', 'Not enough leave quota to accept this broadcast.');
        }
    }


    public function rejectBroadcast($id)
    {
        $broadcast = $this->broadcastService->getBroadcastById($id);

        if (!$broadcast) {
            return redirect()->back()->with('error', 'Broadcast not found');
        }

        $user = Auth::user();

        // Cek apakah user sudah merespons
        $hasResponded = collect($broadcast['responses'])->contains(function ($response) use ($user) {
            return $response['userId'] == $user->id;
        });

        if ($hasResponded) {
            return redirect()->route('employee-dashboard')->with('error', 'You have already responded to this broadcast.');
        }

        // Kirim response "rejected" ke API Spring Boot
        $responseData = [
            'broadcastId' => $broadcast['id'],
            'userId' => $user->id,
            'response' => 'rejected',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        $result = $this->broadcastResponseService->createBroadcastResponse($responseData);


        if (!$result) {
            return redirect()->back()->with('error', 'Failed to submit rejection.');
        }

        return redirect()->route('employee-dashboard')->with('success', 'Broadcast rejected.');
    }


    public function showMailbox()
    {
        $user = Auth::user();

        // Mengambil semua broadcast melalui BroadcastAPIService
        $broadcasts = $this->broadcastService->getAllBroadcasts();

        if (isset($broadcasts['error'])) {
            // Jika terjadi error dalam mengambil data dari API
            return redirect()->back()->with('error', 'Failed to fetch broadcasts: ' . $broadcasts['error']);
        }

        usort($broadcasts, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        // Kirim data broadcasts ke view
        return view('view-mailbox', compact('broadcasts'));
    }


    // public function clockIn(Request $request)
    // {
    //     $user = Auth::user();
    //     $today = Carbon::today();


    //     // Cek apakah user sudah clock in hari ini
    //     $hasAttendance = UserAttendance::where('email', $user->email)
    //         ->whereDate('clock_in', $today)
    //         ->exists();

    //     if ($hasAttendance) {
    //         return back()->with('error', 'Anda sudah melakukan Clock In hari ini.');
    //     }

    //     // Validasi input
    //     $request->validate([
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'verification' => 'required' // Foto wajib dikirim
    //     ]);

    //     // Koordinat kantor dari .env
    //     $officeLatitude = config('app.office_latitude');
    //     $officeLongitude = config('app.office_longitude');
    //     $radius = config('app.office_radius');

    //     // Hitung jarak dengan Haversine Formula
    //     $distance = $this->calculateDistance(
    //         $officeLatitude,
    //         $officeLongitude,
    //         $request->latitude,
    //         $request->longitude
    //     );

    //     if ($distance > $radius) {
    //         return response()->json(['error' => 'Anda berada di luar area yang diizinkan.'], 403);
    //     }


    //     $imageData = $request->verification;

    //     // Simpan foto dari Base64 ke file
    //     $imageName = 'clockin_' . $user->id . '_' . time() . '.png';
    //     $imagePath = 'attendance_photos/' . $imageName;

    //     Storage::disk('public')->put($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));

    //     UserAttendance::create([
    //         'email' => $user->email,
    //         'clock_in' => now(),
    //         'verification' => $imagePath
    //     ]);


    //     return back()->with('success', 'Clock In berhasil.');
    // }



    public function clockIn(Request $request, UserAttendanceAPIService $attendanceService)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Validasi input
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'verification' => 'required' // Foto wajib dikirim
        ]);

        // Cek apakah user sudah clock in hari ini (dari API)
        $attendances = $attendanceService->getAllUserAttendances();

        $alreadyClockedIn = false;
        if (is_array($attendances)) {
            $alreadyClockedIn = collect($attendances)->contains(function ($attendance) use ($user, $today) {
                return isset($attendance['email']) &&
                    $attendance['email'] === $user->email &&
                    Carbon::parse($attendance['clock_in'])->isSameDay($today);
            });
        }

        if ($alreadyClockedIn) {
            return back()->with('error', 'Anda sudah melakukan Clock In hari ini.');
        }

        $officeLatitude = config('app.office_latitude');
        $officeLongitude = config('app.office_longitude');
        $radius = config('app.office_radius');

        // Hitung jarak dengan Haversine Formula
        $distance = $this->calculateDistance(
            $officeLatitude,
            $officeLongitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > $radius) {
            return back()->with('error', 'Anda berada di luar area yang diizinkan. Jarak: ' . round($distance) . ' meter, Radius: ' . $radius . ' meter.');
        }

        // Simpan foto dari Base64 ke file
        $imageData = $request->verification;
        $imageName = 'clockin_' . $user->id . '_' . time() . '.png';
        $imagePath = 'attendance_photos/' . $imageName;

        $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData), true);
        if ($decodedImage === false) {
            return back()->with('error', 'Gagal mendekode gambar.');
        }

        if (!Storage::disk('public')->put($imagePath, $decodedImage)) {
            return back()->with('error', 'Gagal menyimpan gambar.');
        }

        // Pertama, dapatkan user dari API untuk memastikan user ada di database Spring Boot
        $userApiData = $attendanceService->getUserByEmail($user->email);

        // TAMBAHKAN KODE INI (MENGGANTI BAGIAN YANG LAMA)
        // Periksa apakah data user berhasil didapatkan dan tidak ada error
        if (isset($userApiData['error'])) {
            // Jika user belum pernah absen, perlu buat user terlebih dahulu atau gunakan endpoint lain
            Log::error('Failed to get user data', [
                'error' => $userApiData['error']
            ]);
            return back()->with('error', 'Gagal menemukan data user: ' . $userApiData['error']);
        }

        // Pastikan ID user tersedia
        if (!isset($userApiData['id'])) {
            return back()->with('error', 'Data user tidak lengkap, ID tidak ditemukan');
        }
        // AKHIR DARI KODE YANG DITAMBAHKAN

        // Format data sesuai dengan yang diharapkan oleh endpoint POST di Spring Boot
        $attendanceData = [
            'clock_in' => now()->toDateTimeString(),
            'clock_out' => null,
            'total_hours' => null,
            'verification' => $imagePath,
            'user' => [
                'email' => $user->email // ini kirim email, sesuai Spring Boot model
            ]
        ];


        // Log data yang akan dikirim ke API
        Log::info('Sending attendance data to API:', $attendanceData);

        $response = $attendanceService->createUserAttendance($attendanceData);

        if (!$response || isset($response['error'])) {
            Log::error('Failed to create attendance', [
                'request' => $attendanceData,
                'response' => $response
            ]);
            return back()->with('error', 'Gagal menyimpan data ke server absensi: ' .
                (isset($response['error']) ? $response['error'] : 'Tidak ada respons dari server'));
        }

        return back()->with('success', 'Clock In berhasil.');
    }


    // public function clockOut()
    // {
    //     $user = Auth::user();
    //     $today = Carbon::today();

    //     // Ambil data Clock In terbaru
    //     $attendance = UserAttendance::where('email', $user->email)
    //         ->whereDate('clock_in', $today)
    //         ->first();

    //     if (!$attendance) {
    //         return back()->with('error', 'Silakan Clock In terlebih dahulu.');
    //     }

    //     if (!is_null($attendance->clock_out)) {
    //         return back()->with('error', 'Anda sudah melakukan Clock Out.');
    //     }

    //     // Update clock_out
    //     $attendance->update([
    //         'clock_out' => Carbon::now(),
    //         'total_hours' => Carbon::parse($attendance->clock_in)->diffInHours(Carbon::now())
    //     ]);

    //     return redirect()->route('employee-dashboard')->with('success', 'Clock Out berhasil.');
    // }

    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Log user and date
        Log::info('ClockOut: User ' . $user->email . ' trying to clock out on ' . $today);

        // Ambil semua data absensi user
        try {
            $attendances = app(UserAttendanceAPIService::class)->getAllUserAttendances();
            Log::info('ClockOut: Retrieved attendances for user ' . $user->email);
        } catch (\Exception $e) {
            Log::error('ClockOut: Failed to get attendances for user ' . $user->email . '. Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data absensi.');
        }

        // Temukan absensi hari ini
        $attendanceToday = collect($attendances)->first(function ($att) use ($user, $today) {
            // Debugging log untuk melihat isi data attendance
            Log::info('ClockOut: Checking attendance data: ' . json_encode($att));

            // Memastikan hanya membandingkan tanggal, tanpa waktu
            $clockInDate = \Carbon\Carbon::parse($att['clock_in'])->toDateString();

            // Pastikan user email cocok dan tanggal clock_in sama dengan hari ini
            if (
                isset($att['email']) && $att['email'] === $user->email &&
                isset($att['clock_in']) && $clockInDate === $today &&
                empty($att['clock_out'])
            ) {
                return true;
            }

            // Log jika data tidak sesuai
            Log::warning('ClockOut: Invalid attendance data for user ' . $user->email . ': ' . json_encode($att));
            return false;
        });

        if (!$attendanceToday) {
            Log::warning('ClockOut: No attendance found for user ' . $user->email . ' on ' . $today);
            return back()->with('error', 'Silakan Clock In terlebih dahulu.');
        }

        if (!empty($attendanceToday['clock_out'])) {
            Log::info('ClockOut: User ' . $user->email . ' already clocked out for today.');
            return back()->with('error', 'Anda sudah melakukan Clock Out.');
        }

        // Update via API
        try {
            $clockOutTime = Carbon::now();
            $clockInTime = Carbon::parse($attendanceToday['clock_in']);

            // Gunakan floatDiffInHours() untuk mendapatkan hasil dengan desimal
            $totalHours = $clockInTime->floatDiffInHours($clockOutTime);

            // Format ke 2 angka di belakang koma
            $totalHours = round($totalHours, 2);

            $updatePayload = [
                'clock_in' => $attendanceToday['clock_in'],
                'clock_out' => $clockOutTime->toDateTimeString(),
                'total_hours' => $totalHours,
                'verification' => $attendanceToday['verification'] ?? null,
                'user' => [
                    'email' => $user->email // Menggunakan email dari user yang terautentikasi
                ]
            ];

            Log::info('ClockOut: Update payload: ' . json_encode($updatePayload));

            Log::info('ClockOut: Sending update request for attendance ID ' . $attendanceToday['id']);
            $result = app(UserAttendanceAPIService::class)->updateUserAttendance($attendanceToday['id'], $updatePayload);

            if (isset($result['error'])) {
                Log::error('ClockOut: Failed to update attendance ID ' . $attendanceToday['id'] . '. Error: ' . $result['error']);
                return back()->with('error', 'Gagal melakukan Clock Out: ' . $result['error']);
            }

            Log::info('ClockOut: Successfully updated clock out for user ' . $user->email);
        } catch (\Exception $e) {
            Log::error('ClockOut: Failed during clock out process for user ' . $user->email . '. Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat proses Clock Out.');
        }

        return redirect()->route('employee-dashboard')->with('success', 'Clock Out berhasil.');
    }




    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
