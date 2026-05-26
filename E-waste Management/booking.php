<?php
// booking.php — User's personal booking status page
session_start();
require_once 'auth_check.php';
require_login();
include 'db.php';

$user_id = (int) $_SESSION['user_id'];

// Fetch only this user's bookings, most recent first
$stmt = $conn->prepare(
    "SELECT * FROM ewaste_booking WHERE user_id = ? ORDER BY created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Bookings — E-Waste Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --green: #34c759; --red: #ff3b30; --yellow: #ffc107;
            --blue: #007bff; --border: #dee2e6; --light: #f8f9fa;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; padding: 24px; }

        .container {
            max-width: 1050px;
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

        .nav-links { display: flex; gap: 10px; }

        .btn-nav {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            transition: background .2s;
        }

        .btn-primary { background: var(--blue);  color: #fff; }
        .btn-primary:hover { background: #0056b3; }

        .btn-success { background: var(--green); color: #fff; }
        .btn-success:hover { background: #28a745; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 13px 15px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }

        th { background: var(--light); font-weight: 600; color: #444; }
        tr:hover { background: #f9fbfd; }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pending  { background: var(--yellow); color: #000; }
        .badge-accepted { background: var(--green);  color: #fff; }
        .badge-rejected { background: var(--red);    color: #fff; }

        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #999;
        }

        .no-data a {
            display: inline-block;
            margin-top: 14px;
            background: var(--green);
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { display: none; }
            td { padding: 10px 14px; border: none; border-bottom: 1px solid #eee; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-list-alt"></i> My Bookings</h1>
        <div class="nav-links">
            <a href="Schedule.php" class="btn-nav btn-success">+ New Pickup</a>
            <a href="user.php"     class="btn-nav btn-primary">← Home</a>
        </div>
    </header>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Scheduled Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $s = $row['status'];
                        $badge = match($s) {
                            'accepted' => 'badge-accepted',
                            'rejected' => 'badge-rejected',
                            default    => 'badge-pending',
                        };
                    ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= (int)$row['quantity'] ?></td>
                        <td><span class="badge <?= $badge ?>"><?= ucfirst($s) ?></span></td>
                        <td><?= htmlspecialchars(substr($row['created_at'], 0, 10)) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">
            <p style="font-size:40px; margin-bottom:12px;">📭</p>
            <p>You have no bookings yet.</p>
            <a href="Schedule.php">Schedule Your First Pickup</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
