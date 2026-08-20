<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Work Project')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <style>
    body {
        background-color: #f8f9fa;
        display: flex;
    }

    .container {
        margin-top: 10%; /* Sesuaikan nilai margin-top sesuai kebutuhan */
    }
    .login-card {
        width: 100%;
        height: 370px;   /* Atur tinggi form */
        max-width: 450px; /* Atur lebar form */
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background-color: white;
    }

    .register-card {
        width: 100%;
        height: 520px;   /* Atur tinggi form */
        max-width: 450px; /* Atur lebar form */
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background-color: white;
    }

    .card-header {
        border-radius: 10px 10px 0 0;
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 18px;
    }

    .card-header.header-login {
        border-radius: 10px 10px 0 0;
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 18px;
    }
    .card-header.header-register {
        border-radius: 10px 10px 0 0;
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 18px;
    }
    .btn-login {
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-login:hover {
        background-color: #0056b3;
    }
</style>
  <body>
    @yield("content")
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>