<!-- register.php -->
<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check password length
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Save user information (basic session example; replace with DB in production)
        $_SESSION['registered_users'][$email] = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT) // Hash the password
        ];
        $_SESSION['success_message'] = "Registration successful! Please login.";
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('map.bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .register-form {
            background-color: #c19a6b;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: #fff;
        }
        .form-control, .btn-primary {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="register-form">
        <h2 class="text-center">Register</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-dark btn-block">Register</button>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>