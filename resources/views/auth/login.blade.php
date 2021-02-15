<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Authors Consulting</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">    
    <!--<script src="https://use.fontawesome.com/releases/v5.7.2/js/all.js" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <script src="{{ asset('custom/fontawesome_all.js') }}" data-auto-replace-svg="nest"></script>
    <link rel="stylesheet" href="{{ asset('custom/font-awesome.min.css') }}">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

    <!-- Outer Row -->
        <div class="row justify-content-center mt-5" style="margin-top: 0.8rem !important">

            <div class="col-xl-10 col-lg-12 col-md-9 mt-5">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image text-center"><img src="{{ asset('storage/image/logo.jpg') }}" style="margin-top: 4rem;" /></div>
                            <div class="col-lg-6" style="background-color: #f5f6f7;">
                                <div class="p-5">
                                    <div class="mb-3">
                                        <h1 class="h4 mb-4 color-blue" style="font-size: 3rem">System Login</h1>
                                        <label class='small'>Enter your login details below</label>

                                    </div>
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            
                                            <!-- <div class="form-group">
                                                <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required aria-describedby="emailHelp" placeholder="Enter Email Address...">

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div> -->
                                            <!-- Kamlesh -->
                                            <div class="input-group mb-2" style="margin-bottom: 1.5rem !important;">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required aria-describedby="emailHelp" placeholder="Email address">
                                                 @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>


                                            <div class="input-group mb-3" style="margin-bottom: 1.5rem !important;">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                                                </div>
                                                <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required placeholder="Password">
                                            </div>

                                            <!-- end Kamlesh -->
<!-- 
                                            <div class="form-group">
                                                <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" required placeholder="Password">

                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div> -->

                                            <div class="form-group">
                                                <?php
                                                    $current_year = date('Y');
                                                ?>
                                                <select id="year" class="form-control{{ $errors->has('year') ? ' is-invalid' : '' }}" name="year" required>
                                                    <option value=''>Choose Year</option>
                                                    <?php
                                                        for($y=2020; $y<=$current_year; $y++) {
                                                            if($y == $current_year) {
                                                                echo "<option value='".$y."' selected>".$y."</option>";
                                                            } else {
                                                                echo "<option value='".$y."'>".$y."</option>";    
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                                {{ __('Login') }}
                                            </button>
                                            <br />
                                           <!-- <hr> -->
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

     <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>

</html>
