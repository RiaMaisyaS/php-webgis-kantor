<?php
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Generate a temporary reset link (for simplicity) or send a temporary password
        $temp_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

        // Update the password in the database (optional if using reset link method)
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
        mysqli_query($connection, $update_query);

        // Send the temporary password to the user's email
        $to = $email;
        $subject = "Password Reset Request";
        $message = "Your temporary password is: $temp_password\nPlease log in and change your password.";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($to, $subject, $message, $headers)) {
            $message = "A temporary password has been sent to your email.";
        } else {
            $message = "Failed to send email. Please try again.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Forgot Password</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
    </form>
</div>
</body>
</html>
