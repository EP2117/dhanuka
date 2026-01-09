<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>DHANUKA - Login</title>
    <link rel="icon" href="{{ asset('storage/images/logo.jpg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            /* Gray Scale Palette */
            --gray-darkest: #2C3E50;
            --gray-dark: #34495E;
            --gray-medium: #5D6D7E;
            --gray-light: #85929E;
            --gray-lighter: #AEB6BF;
            --gray-lightest: #D5DBDB;
            --gray-bg: #ECF0F1;
            --gray-bg-light: #F8F9FA;
            --white: #FFFFFF;
            --error: #E74C3C;
            --success: #27AE60;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--gray-dark) 0%, var(--gray-darkest) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            z-index: 10;
        }

        .login-card {
            background: var(--white);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.6s ease-out;
            border: 1px solid var(--gray-lightest);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gray-darkest) 0%, var(--gray-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.3);
            border: 3px solid var(--gray-light);
        }

        .logo-wrapper i {
            font-size: 45px;
            color: var(--white);
        }

        .logo-wrapper img {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-darkest);
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--gray-light);
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-darkest);
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-medium);
            font-size: 16px;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .form-control:focus+.input-icon,
        .form-select:focus~.input-icon {
            color: var(--gray-darkest);
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid var(--gray-lightest);
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--gray-bg-light);
            color: var(--gray-darkest);
        }

        .form-control::placeholder {
            color: var(--gray-lighter);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gray-medium);
            box-shadow: 0 0 0 4px rgba(93, 109, 126, 0.15);
            background: var(--white);
        }

        .form-control.is-invalid {
            border-color: var(--error);
        }

        .invalid-feedback,
        .text-danger {
            display: block;
            font-size: 13px;
            color: var(--error);
            margin-top: 6px;
            padding-left: 4px;
        }

        .form-select {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid var(--gray-lightest);
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--gray-bg-light);
            color: var(--gray-darkest);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235D6D7E' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--gray-medium);
            box-shadow: 0 0 0 4px rgba(93, 109, 126, 0.15);
            background-color: var(--white);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--gray-darkest) 0%, var(--gray-dark) 100%);
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(44, 62, 80, 0.5);
            background: linear-gradient(135deg, var(--gray-dark) 0%, var(--gray-medium) 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 15%;
            left: 15%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            right: 20%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            33% {
                transform: translateY(-30px) translateX(20px);
            }

            66% {
                transform: translateY(-10px) translateX(-20px);
            }
        }

        /* Additional Subtle Enhancements */
        .form-control:hover,
        .form-select:hover {
            border-color: var(--gray-lighter);
        }

        /* Focus states for better accessibility */
        .btn-login:focus {
            outline: 3px solid var(--gray-light);
            outline-offset: 2px;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
        }

        /* Loading state for button (optional) */
        .btn-login:disabled {
            background: linear-gradient(135deg, var(--gray-light) 0%, var(--gray-lighter) 100%);
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 40px 30px;
            }

            .login-title {
                font-size: 24px;
            }

            .logo-wrapper {
                width: 90px;
                height: 90px;
            }

            .logo-wrapper i {
                font-size: 40px;
            }
        }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <div class="mb-3">
                    <img class="" src="{{ asset('storage/images/logo.jpg') }}" width="150"  alt="">
                </div>
                <h1 class="login-title">System Login</h1>
                <p class="login-subtitle">Enter your credentials to continue</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="your.email@example.com" required>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter your password" required>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Year -->
                <div class="form-group">
                    <label class="form-label" for="year">Financial Year</label>
                    <div class="input-wrapper">
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <select id="year" name="year" class="form-select" required>
                            <option value="">Select Year</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            {{-- <option value="2024July">2024 July to December</option> --}}
                            <option value="2025">2025</option>
                            <option value="2026" selected>2026</option>
                        </select>
                    </div>
                    @error('year')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
