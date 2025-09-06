<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Role</title>
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
                <img src="./images/CTU-logo.png" class="logo" alt="CTU Logo">
            </div>
            <form action="./action/roleSelection.php" method="POST" class="card-body">
                <label for="role">Choose a Role</label>
                <select name="role" id="role">
                    <option value="select">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="instructor">Instructor</option>
                    <option value="student">Student</option>
                </select>
                <button type="submit" class="btn-drk-bg">Confirm</button>
            </form>
        </div>
    </main>
</body>
</html>