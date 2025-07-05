<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin/Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <main class="container">
        <div class="wrapper">
            <div class="signin-signup-container">
                <div class="signin-signup-body">
                    <div class="signin-body">
                        <div class="card-header">
                            <div>
                                <h2>Cebu Technological University</h2>
                                <h3>Learning Management System</h3>
                            </div>
                            <img src="./images/CTU-logo.png" class="logo" alt="CTU Logo">
                        </div>
                        <!-- SIGN IN !-->
                        <form action="" method="POST" class="signin-form form-body">
                            <h2 class="title">Sign in to your account</h2>
                            <div class="input-field">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" id="e-mail" name="e-mail" placeholder="E-mail" required>
                            </div>
                            <div class="input-field">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Password" required>
                                <i class="fa-regular fa-eye toggle-password" toggle="#password"></i>
                            </div>
                            
                            <button type="submit" class="btn-drk-bg">Sign in</button>
                            <p class="account-text">Don't have an account? <a href="#" id="signup-btn2">Sign up</a></p>
                        </form>
                    </div>
                    <!-- SIGN UP !-->
                    <form action="./action/signup.php" method="POST" class="signup-form form-body" id="signup">
                        <h2 class="title">Create Account</h2>
                        <span class="error-message"></span>
                        <div class="input-field">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="firstName" name="firstname" placeholder="First Name" required maxlength="20">
                        </div>
                        <div class="input-field">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="lastName" name="lastName" placeholder="Last Name" required maxlength="20">
                        </div>
                        <div class="input-field">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="E-mail" required>
                        </div>
                        <div class="input-field">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="signup-password" name="password" placeholder="Password" required>
                            <i class="fa-regular fa-eye toggle-password" toggle="#signup-password"></i>
                        </div>
                        <div class="input-field">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="confirmPass" name="confirmPass" placeholder="Re-enter Password" required>
                            <i class="fa-regular fa-eye toggle-password" toggle="#confirmPass"></i>
                        </div>
                            
                        <button type="submit" class="btn-drk-bg">Sign up</button>
                        <p class="account-text">Already have an account? <a href="#" id="signin-btn2">Sign in</a></p>
                    </form>
                </div>
                <div class="panels-container">
                    <div class="panel left-panel">
                        <div class="content">
                            <h3>Welcome Back!</h3>
                            <p>To keep connected with us Sign in with your personal information</p>
                            <button class="btn-light-bg" id="signin-btn">Sign in</button>
                        </div>
                    </div>
                    <div class="panel right-panel">
                        <div class="content">
                            <h3>New here?</h3>
                            <p>Sign up to gain access to our learning materials, resources, and more.</p>
                            <button class="btn-light-bg" id="signup-btn">Sign up</button>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </main>
    <script src="./js/flipPages.js"></script>
    <script src="./js/toggle.js"></script>
    <script src="./js/passwordValidation.js"></script>
</body>
</html>