@extends("layouts.default")
@section("title", "Login")
@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            @if(session()->has("success"))
            <div class = "alert alert-success">
                {{session()->get("success")}}
            </div>
             @endif
            @if(session()->has("error"))
            <div class = "alert alert-error">
            {{session()->get("error")}}
             </div>
            @endif
            <div class="card login-card mt-5">
                <div class="card-header header-login bg-primary text-white">
                    <h3 class="card-title text-center">Login</h3>
                </div>
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

                    <form method="POST" action={{route("login.post")}}>
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email Anda">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password Anda">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-login">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection