<?php
session_start();
$signinError = $_SESSION['signin_error'] ?? '';
unset($_SESSION['signin_error']);
?>

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
                        <form action="./action/signin.php" method="POST" class="signin-form form-body">
                            <h2 class="title">Sign in to your account</h2>
                            <span class="signin-error-message"><?= htmlspecialchars($signinError) ?></span>
                            <div class="input-field">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" id="e-mail" name="eMail" placeholder="E-mail" required>
                            </div>
                            <div class="input-field">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Password" required>
                                <i class="fa-regular fa-eye-slash toggle-password" data-toggle="#password"></i>
                            </div>
                            
                            <button type="submit" class="btn-drk-bg">Sign in</button>
                            <p class="forgot-pass"><a href="forgotPasswordPage.php">Forgot password?</a></p>
                            <p class="account-text">Don't have an account? <a href="#" id="signup-btn2">Sign up</a></p>
                        </form>
                    </div>
                    <!-- SIGN UP !-->
                    <form class="signup-form form-body" id="signup">
                        <div class="signup-header">
                            <ul>
                                <li class="active form_1_progress">
                                    <div>
                                        <p>1</p>
                                    </div>
                                </li>
                                <li class="form_2_progress">
                                    <div>
                                        <p>2</p>
                                    </div>
                                </li>
                                <li class="form_3_progress">
                                    <div>
                                        <p>3</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="progress-content">
                            <!-- PROGRESS 1 -->
                            <div class="progress1">
                                <h2>Enter account details</h2>
                                    <div class="form-step1">
                                        <span class="error-message"></span>
                                        <div class="input-field">
                                            <i class="fa-regular fa-envelope"></i>
                                            <input type="email" id="email" name="email" placeholder="E-mail" required>
                                        </div>
                                        <div class="input-field">
                                            <i class="fa-solid fa-lock"></i>
                                            <input type="password" id="signup-password" name="signup-password" placeholder="Password" required>
                                            <i class="fa-regular fa-eye-slash toggle-password" data-toggle="#signup-password"></i>
                                        </div>
                                        <div class="input-field">
                                            <i class="fa-solid fa-lock"></i>
                                            <input type="password" id="confirmPass" name="confirmPass" placeholder="Re-enter Password" required>
                                            <i class="fa-regular fa-eye-slash toggle-password" data-toggle="#confirmPass"></i>
                                        </div>
                                    </div>
                            </div>

                            <!-- PROGRESS 2 -->
                            <div class="progress2" style="display: none;">
                                <h2>Just a few more things...</h2>
                                    <div class="form-step2">
                                        <div class="role-selection">
                                            <input type="hidden" name="role" id="roleInput">
                                            <button type="button" class="btn-transparent-bg roleBtn"><i class="fa-solid fa-user-tie"></i>Instructor</button>
                                            <button type="button" class="btn-transparent-bg roleBtn"><i class="fa-solid fa-user"></i>Student</button>
                                        </div>
                                        
                                        <div class="input-field">
                                            <i class="fa-regular fa-user"></i>
                                            <input type="text" id="firstName" name="firstname" placeholder="First Name" maxlength="20">
                                        </div>
                                        <div class="input-field">
                                            <i class="fa-regular fa-user"></i>
                                            <input type="text" id="lastName" name="lastName" placeholder="Last Name" maxlength="20">
                                        </div>

                                        <!-- Privacy Policy Checkbox -->
                                        <div class="terms-field">
                                            <input type="checkbox" id="agreePrivacy" name="agreePrivacy" required>
                                            <label for="agreePrivacy">
                                                I agree to the <a href="https://www.termsfeed.com/live/8120927a-a5af-40d3-851e-7e362446399d" target="_blank">Privacy Policy</a>
                                            </label>
                                        </div>
                                    </div>
                            </div>

                            <!-- PROGRESS 3 -->
                            <div class="progress3" style="display: none;">
                                <h2>Verify your email</h2>
                                    <div class="form-step3">
                                        <p>
                                            We've sent a verification code to your email address. Please enter the code below to verify your account.
                                        </p>
                                        <div class="input-field">
                                            <i class="fa-solid fa-envelope-open-text"></i>
                                            <input type="text" id="emailCode" name="emailCode" placeholder="Enter code" maxlength="6">
                                        </div>
                                    </div>
                            </div>
                        </div>

                        <!-- BUTTONS -->
                         <div class="progress-btns">
                            <!-- PROGRESS 1 BUTTON -->
                            <div class="common-btns progress1-btns">
                                <button type="button" class="btn-drk-bg next-btn">Next</button>
                            </div>

                            <!-- PROGRESS 2 BUTTONS -->
                            <div class="common-btns progress2-btns" style="display: none;">
                                <button type="submit" class="btn-drk-bg submit-btn">Sign up</button>
                            </div>

                            <!-- PROGRESS 3 BUTTONS -->
                            <div class="common-btns progress3-btns" style="display: none;">
                                <button type="submit" class="btn-drk-bg verify-btn" id="verifyBtn">Verify</button>
                            </div>
                         </div>
                            
                        <!-- <button type="submit" class="btn-drk-bg">Sign up</button> -->
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

                <div class="overlay" id="loadingOverlay">
                    <div class="spinner">⏳ Loading...</div>
                </div>
            </div>
        </div>
    </main>
    <script src="./js/flipPages.js"></script>
    <script src="./js/toggle.js"></script>
    <script src="./js/passwordValidation.js"></script>
</body>
</html>