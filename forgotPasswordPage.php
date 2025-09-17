<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <main class="container wrapper">
        <div class="role-card">
            <a href="index.php" class=""><i class="fa-solid fa-circle-arrow-left"></i></a>
            <form action="./action/forgotPass.php" method="POST" class="forgot-password-form card-body" id="forgot-password">
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
                                <input type="email" id="email" name="email" placeholder="Enter registered email" required>
                            </div>
                        </div>
                    </div>

                    <!-- PROGRESS 2 -->
                    <div class="progress2" style="display: none;">
                        <h2>Verify your email</h2>
                        <div class="form-step2">
                            <p>
                                We've sent a verification code to your email address. Please enter the code below to verify your account.
                            </p>
                            <div class="input-field">
                                <i class="fa-solid fa-envelope-open-text"></i>
                                <input type="text" id="emailCode" name="emailCode" placeholder="Enter code" maxlength="6">
                            </div>
                        </div>
                    </div>

                    <!-- PROGRESS 3 -->
                    <div class="progress3" style="display: none;">
                        <h2>Enter Password...</h2>
                        <div class="form-step3">
                            <div class="input-field">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="signup-password" name="signup-password" placeholder="Password" required>
                                <i class="fa-regular fa-eye-slash toggle-password" toggle="#signup-password"></i>
                            </div>
                            <div class="input-field">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="confirmPass" name="confirmPass" placeholder="Re-enter Password" required>
                                <i class="fa-regular fa-eye-slash toggle-password" toggle="#confirmPass"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="progress-btns">
                    <!-- PROGRESS 1 BUTTON -->
                    <div class="common-btns progress1-btns">
                        <button type="button" class="btn-drk-bg next-btn">Next</button>
                    </div>

                    <!-- PROGRESS 2 BUTTONS -->
                    <div class="common-btns progress2-btns" style="display: none;">
                        <button type="button" class="btn-drk-bg verify-btn" id="verifyBtn">Verify</button>
                    </div>

                    <!-- PROGRESS 3 BUTTONS -->
                    <div class="common-btns progress3-btns" style="display: none;">
                        <button type="button" class="btn-drk-bg reset-btn" id="resetBtn">Reset Password</button>
                    </div>
                </div>
                
            </form>
            <div class="overlay" id="loadingOverlay">
                <div class="spinner">⏳ Loading...</div>
            </div>
        </div>
    </main>
    <script src="./js/passwordValidation.js"></script>
</body>
</html>