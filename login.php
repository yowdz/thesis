<?php
session_start();
require 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['course_id'] = $user['course_id']; // Store course_id if needed
        
        // Handle "Keep me logged in" functionality
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + 86400 * 30, '/'); // 30 days
            // Store token in database (you'll need to add a 'remember_token' column to your users table)
        }
        
        // Redirect based on role
        if ($user['role'] === 'course_admin') {
            header('Location: ca_dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivarez College - Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-overlay"></div>
    
    <div class="login-container">
        <img src="bg/oc.png" alt="Olivarez College Logo" class="login-logo">
        <h1 class="login-title">Olivarez College</h1>
        <h2 class="login-subtitle">Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form class="login-form" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter Your Username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
            </div>
            
            <div class="keep-logged">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me logged in</label>
            </div>
            
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>