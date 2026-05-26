<?php
session_start();
require_once 'auth_check.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — E-Waste Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f4f8;
            color: #333;
        }

        .navbar {
            background: #2980b9;
            padding: 18px 30px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }

        .navbar h1 { font-size: 22px; font-weight: 700; }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            background: rgba(255,255,255,0.2);
            padding: 7px 14px;
            border-radius: 20px;
            transition: background .2s;
        }

        .navbar a:hover { background: rgba(255,255,255,0.35); }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.07);
            transition: transform .25s, box-shadow .25s;
            border-top: 4px solid #2980b9;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .card .icon { font-size: 36px; margin-bottom: 12px; }

        .card h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .card a {
            display: inline-block;
            padding: 9px 18px;
            background: #2980b9;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }

        .card a:hover { background: #1a6fa0; }

        @media (max-width: 600px) {
            .container { margin: 30px auto; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>🛠️ Admin Dashboard</h1>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <div class="card">
        <div class="icon">👥</div>
        <h2>Manage Users</h2>
        <p>View all registered users on the platform.</p>
        <a href="manage_users.php">Manage Users →</a>
    </div>

    <div class="card">
        <div class="icon">📦</div>
        <h2>Pickup Requests</h2>
        <p>Review and accept or reject pending e-waste pickup requests.</p>
        <a href="manage_pickups.php">Manage Pickups →</a>
    </div>

    <div class="card">
        <div class="icon">📋</div>
        <h2>Accepted History</h2>
        <p>Browse all accepted/completed pickup records.</p>
        <a href="history.php">View History →</a>
    </div>
</div>

</body>
</html>
