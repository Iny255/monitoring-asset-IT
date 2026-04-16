@extends('layouts/blankLayout')

@section('title', 'Login')

@section('content')

    <style>
        body {
            background: #f4f6fb;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* WRAPPER */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
               LEFT (IMAGE)
            ========================= */
        .login-left {
            flex: 1.3;
            position: relative;
            overflow: hidden;
        }

        .login-left img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-left::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to right,
                    rgba(255, 255, 255, 0.2),
                    rgba(255, 255, 255, 0.7));
        }

        /* =========================
               RIGHT (FORM)
            ========================= */
        .login-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8fafc;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            padding: 50px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
        }

        /* LOGO */
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 120px;
        }

        /* TEXT */
        .title {
            font-size: 26px;
            font-weight: 600;
            text-align: center;
            color: #1e293b;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 30px;
        }

        /* FORM */
        .form-control {
            height: 50px;
            border-radius: 10px;
            font-size: 14px;
        }

        .input-group-text {
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-login {
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(90deg, #4f46e5, #6366f1);
            border: none;
            font-weight: 600;
            color: #ffffff !important;
            /* 🔥 paksa putih */
        }

        .btn-login:hover {
            color: #ffffff !important;
            /* biar tidak berubah abu saat hover */
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 20px;
        }

        /* =========================
               MOBILE
            ========================= */
        @media(max-width: 992px) {

            .login-wrapper {
                flex-direction: column;
            }

            .login-left {
                display: none;
            }

            .login-right {
                align-items: flex-start;
                padding: 20px;
            }

            .login-card {
                max-width: 100%;
                width: 100%;
                padding: 30px 20px;
                border-radius: 16px;
                box-shadow: none;
            }

            .title {
                font-size: 22px;
            }

            .subtitle {
                font-size: 13px;
                margin-bottom: 20px;
            }

            .form-control {
                height: 45px;
            }

            .btn-login {
                height: 45px;
                font-size: 14px;
            }
        }
    </style>

    <div class="login-wrapper">

        <!-- LEFT IMAGE -->
        <div class="login-left">
            <img src="{{ asset('assets/img/login.jpeg') }}" alt="Login Image">
        </div>

        <!-- RIGHT FORM -->
        <div class="login-right">

            <div class="login-card">

                <!-- LOGO -->
                <div class="logo">
                    <img src="{{ asset('assets/img/logo9.png') }}">
                </div>

                <div class="title">Monitoring Asset</div>
                <div class="subtitle">Silakan login untuk melanjutkan</div>

                {{-- ALERT --}}
                @if (session('success'))
                    <div class="alert alert-success py-2">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Password" required>

                            <span class="input-group-text" id="togglePassword">
                                <i class="bx bx-hide"></i>
                            </span>
                        </div>
                    </div>

                    <button class="btn btn-login w-100">
                        Login
                    </button>

                    <div class="footer-text">
                        © 2026 IT Developer Sembilan Group
                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        // toggle password
        document.getElementById("togglePassword").addEventListener("click", function() {
            const input = document.getElementById("password");
            const icon = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("bx-hide", "bx-show");
            } else {
                input.type = "password";
                icon.classList.replace("bx-show", "bx-hide");
            }
        });
    </script>

@endsection
