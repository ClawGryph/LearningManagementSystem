<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Sign in</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <main class="container wrapper">
        <div class="role-card">
            <div class="card-header">
                <div>
                    <h2>Cebu Technological University</h2>
                    <h3>Learning Management System</h3>
                </div>
                <img src="./images/CTU-logo.png" alt="CTU Logo" class="logo">
            </div>
            <form action="" method="POST" class="admin-form card-body">
                <h4 class="title">Sign in to your account</h4>
                <div class="input-field">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="e-mail" name="e-mail" placeholder="E-mail" required>
                </div>
                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="btn-drk-bg">Sign in</button>
            </form>
        </div>
    </main>
</body>
</html>