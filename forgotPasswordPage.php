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
            <form action="./action/forgotPass.php" method="POST" class="forgot-password-form card-body" id="forgot-password">
                <h2 class="title">Reset your password</h2>
                <span class="error-message"></span>
                <div class="input-field">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                </div>
                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="signup-password" name="signup-password" placeholder="Enter new password" required>
                    <i class="fa-regular fa-eye toggle-password" toggle="#signup-password"></i>
                </div>
                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="confirmPass" name="confirmPass" placeholder="Re-enter new password" required>
                    <i class="fa-regular fa-eye toggle-password" toggle="#confirmPass"></i>
                </div>

                <button type="submit" class="btn-drk-bg">Reset Password</button>
            </form>
        </div>
    </main>
    <script src="./js/toggle.js"></script>
    <script src="./js/passwordValidation.js"></script>
</body>
</html>