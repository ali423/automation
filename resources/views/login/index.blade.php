<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>شرکت روغن موتور قم - ورود به حساب کاربری</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo/icon.png') }}" />

    <link rel="stylesheet" href="{{ asset('css/login/bootstrap.rtl.min.css') }}" />
    <link rel="stylesheet" href=" {{ asset('css/login/bootstrap-icons.css') }}" />


    <link rel='stylesheet' type='text/css' media='screen' href='{{ asset('css/login/style.css') }}'>
</head>

<body>
    @include('layouts.errors')
    <div id="login" class="container-fluid">
        <div class="d-flex flex-row-reverse">
            <div class="col-md-8 col-lg-5">
                <div class="card shadow p-4 rounded">
                    <div class="text-center pb-5">
                        <img src="{{ asset('img/logo/darklogo.png') }}" class="logo" alt="qom-engin-oil">
                    </div>
                    <form action="{{ route('login.store') }}" method="post" class="needs-validation" novalidate>
                        @csrf
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-white" id="basic-addon1"><i
                                    class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control" name="user_name" placeholder="نام کاربری"
                                aria-label="Username" aria-describedby="basic-addon1" id="username" required>
                            <div class="invalid-feedback">نام کاربری نمی تواند خالی باشد.</div>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white" id="basic-addon1"><i
                                    class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="رمز عبور"
                                aria-label="Username" aria-describedby="basic-addon1" id="password"
                                pattern=".{8,16}" required>
                            <div class="invalid-feedback">
                                رمز عبور حداقل باید از 8 کاراکتر بیشتر و از اعداد و حروف انگلیسی تشکیل شده باشد.
                            </div>

                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between">
                            <div class="mb-4 form-check form-switch">
                                <label class="form-check-label" for="showpass">نمایش رمز عبور</label>
                                <input class="form-check-input" type="checkbox" role="switch" id="showpass">
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="Remember">مرا به خاطر بسپار</label>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn px-5 btn-warning">ورود</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/login/bootstrap.bundle.min.js') }}"></script>
    <script src='{{ asset('js/login/script.js') }}'></script>

</body>

</html>
