<?php
// login_register.php
session_start();
include('db.php');

// If already logged in, redirect to the right dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'user.php'));
    exit();
}

$form_action = isset($_GET['action']) ? $_GET['action'] : 'login';
// Only 'login' and 'register' are valid actions; register is for users only
$form_action = in_array($form_action, ['login', 'register']) ? $form_action : 'login';

$message = '';

// ── Registration (users only) ────────────────────────────────────────────────
if ($form_action === 'register' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (strlen($username) < 3) {
        $message = "Username must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        // Check for existing username or email
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username or email is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2  = $conn->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt2->bind_param("sss", $username, $email, $hashed);
            if ($stmt2->execute()) {
                $message = "Registration successful! <a href='login_register.php?action=login'>Login here</a>.";
                $form_action = 'login'; // show login form after registration
            } else {
                $message = "Registration failed. Please try again.";
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}

// ── Login ────────────────────────────────────────────────────────────────────
if ($form_action === 'login' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Always query the single `user` table — role is a column, not a table
    $stmt = $conn->prepare("SELECT id, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // prevent session fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'user.php'));
            exit();
        }
    }
    // Intentionally vague — don't reveal which field was wrong
    $message = "Invalid username or password.";
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $form_action === 'register' ? 'Register' : 'Login' ?> — E-Waste Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #fff;
            padding: 40px 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 24px;
            font-size: 26px;
        }

        .message {
            text-align: center;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            background: #fde8e8;
            color: #c0392b;
            font-weight: 500;
        }

        .message a { color: #2980b9; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            margin-bottom: 16px;
            transition: border-color .2s;
        }

        input:focus { outline: none; border-color: #3498db; }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }

        button:hover { background: #2980b9; }

        .switch {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: #555;
        }

        .switch a { color: #3498db; text-decoration: none; font-weight: 600; }
        .switch a:hover { text-decoration: underline; }

        .logo {
            text-align: center;
            font-size: 32px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">♻️</div>
    <h2><?= $form_action === 'register' ? 'Create Account' : 'Sign In' ?></h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($form_action === 'login'): ?>
        <form method="POST" autocomplete="on">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">

            <button type="submit" name="login">Login</button>
        </form>
        <p class="switch">Don't have an account? <a href="?action=register">Register</a></p>
        <p class="switch" style="margin-top:8px;"><a href="index.html">← Back to Home</a></p>

    <?php else: ?>
        <form method="POST" autocomplete="off">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required minlength="3">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="your@email.com" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 6 characters" required minlength="6">

            <button type="submit" name="register">Register</button>
        </form>
        <p class="switch">Already have an account? <a href="?action=login">Login</a></p>
        <p class="switch" style="margin-top:8px;"><a href="index.html">← Back to Home</a></p>
    <?php endif; ?>
</div>
</body>
</html>
