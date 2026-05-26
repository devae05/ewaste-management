<?php
session_start();
require_once 'auth_check.php';
require_login();
include 'db.php';

$is_admin = ($_SESSION['role'] === 'admin');
$user_id  = (int) $_SESSION['user_id'];

// Admins see all accepted bookings; users see only their own
if ($is_admin) {
    $stmt = $conn->prepare(
        "SELECT eb.*, u.username
         FROM ewaste_booking eb
         LEFT JOIN user u ON eb.user_id = u.id
         WHERE eb.status = 'accepted'
         ORDER BY eb.updated_at DESC"
    );
    $stmt->execute();
} else {
    $stmt = $conn->prepare(
        "SELECT eb.*, u.username
         FROM ewaste_booking eb
         LEFT JOIN user u ON eb.user_id = u.id
         WHERE eb.status = 'accepted' AND eb.user_id = ?
         ORDER BY eb.updated_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accepted Bookings — History</title>
    <style>
        :root { --green: #34c759; --border: #dee2e6; --light: #f8f9fa; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; padding: 24px; }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            padding: 30px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        h1 { font-size: 22px; color: #333; }

        .back-btn {
            text-decoration: none;
            background: #007bff;
            color: #fff;
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }

        .back-btn:hover { background: #0056b3; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 13px 15px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }

        th { background: var(--light); font-weight: 600; color: #444; }
        tr:hover { background: #f9fbfd; }

        .badge-accepted {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: var(--green);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }

        .no-data { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>📋 Accepted Bookings History</h1>
        <a href="<?= $is_admin ? 'admin.php' : 'user.php' ?>" class="back-btn">
            ← <?= $is_admin ? 'Dashboard' : 'Home' ?>
        </a>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <?php if ($is_admin): ?><th>User</th><?php endif; ?>
                <th>Date &amp; Time</th>
                <th>Location</th>
                <th>E-Waste</th>
                <th>Status</th>
                <th>Completed On</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <?php if ($is_admin): ?>
                            <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?> &times; <?= (int)$row['quantity'] ?></td>
                        <td><span class="badge-accepted">Accepted</span></td>
                        <td><?= htmlspecialchars($row['updated_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $is_admin ? 7 : 6 ?>" class="no-data">
                        No accepted bookings found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
