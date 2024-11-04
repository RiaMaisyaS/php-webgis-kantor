<!-- login.php -->
<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verify credentials
    if (isset($_SESSION['registered_users'][$email]) && password_verify($password, $_SESSION['registered_users'][$email]['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $_SESSION['registered_users'][$email]['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('map.bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .login-form {
            background-color: #c19a6b;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .login-form h2 {
            color: #fff;
        }
        .login-form .btn-dark {
            background-color: #333;
        }
        .form-label {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2 class="text-center">Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <a href="forgot-password.php" style="color: #fff;">Forgot Password?</a>
            <button type="submit" class="btn btn-dark btn-block">Login</button>
            <p class="text-center mt-3">Don’t have an account? <a href="register.php" style="color: #fff;">Register here</a></p>
            
        </form>
    </div>
</body>
</html>