<?php
session_start();
require_once 'auth_check.php';
require_admin();
include 'db.php';

$result = $conn->query("SELECT id, username, email, role, created_at FROM user ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users — Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'Segoe UI', sans-serif; background: #f5f8fa; padding: 30px 20px; }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 35px 40px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 2px solid #eee;
        }

        h2 { font-size: 24px; color: #2c3e50; }

        .back-btn {
            text-decoration: none;
            background: #3498db;
            color: #fff;
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }

        .back-btn:hover { background: #2980b9; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 13px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        th { background: #f4f6f9; font-weight: 600; color: #444; }
        tr:hover { background: #f9fbfd; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge-admin { background: #fde8e8; color: #c0392b; }
        .badge-user  { background: #e8f4fd; color: #2980b9; }

        .no-data { text-align: center; padding: 30px; color: #999; }

        @media (max-width: 600px) {
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h2>👥 Manage Users</h2>
        <a href="admin.php" class="back-btn">← Dashboard</a>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['role'] ?>">
                                <?= htmlspecialchars($row['role']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="no-data">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
