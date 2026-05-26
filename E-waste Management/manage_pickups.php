<?php
session_start();
require_once 'auth_check.php';
require_admin();
include 'db.php';

$message = '';

// ── Handle Accept / Reject ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    $booking_id  = (int) $_POST['booking_id'];
    $status      = in_array($_POST['action'], ['accepted', 'rejected']) ? $_POST['action'] : null;
    $admin_notes = trim($_POST['admin_notes'] ?? '');

    if ($status && $booking_id > 0) {
        $stmt = $conn->prepare(
            "UPDATE ewaste_booking SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?"
        );
        $stmt->bind_param("ssi", $status, $admin_notes, $booking_id);
        if ($stmt->execute()) {
            $message = ['type' => 'success', 'text' => "Booking #$booking_id has been $status successfully."];
        } else {
            $message = ['type' => 'danger', 'text' => "Error updating booking: " . $stmt->error];
        }
        $stmt->close();
    }
}

// ── Fetch pending bookings with requester username ───────────────────────────
$result = $conn->query(
    "SELECT eb.*, u.username
     FROM ewaste_booking eb
     LEFT JOIN user u ON eb.user_id = u.id
     WHERE eb.status = 'pending'
     ORDER BY eb.date ASC, eb.time ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pickup Requests — Admin</title>
    <style>
        :root {
            --green: #34c759; --green-dark: #28a745;
            --red: #ff3b30; --yellow: #ffc107;
            --blue: #007bff; --light: #f8f9fa;
            --border: #dee2e6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; padding: 24px; }

        .container {
            max-width: 1150px;
            background: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        h1 { font-size: 22px; color: #333; }

        .back-btn {
            text-decoration: none;
            background: var(--blue);
            color: #fff;
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            transition: background .2s;
        }

        .back-btn:hover { background: #0056b3; }

        .alert {
            padding: 12px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success { background: #e6ffed; color: var(--green-dark); border-left: 4px solid var(--green); }
        .alert-danger  { background: #ffe6e6; color: var(--red);        border-left: 4px solid var(--red);   }

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
            font-weight: 600;
        }

        .badge-pending  { background: var(--yellow); color: #000; }
        .badge-accepted { background: var(--green);  color: #fff; }
        .badge-rejected { background: var(--red);    color: #fff; }

        .actions form { display: inline; }

        .btn {
            border: none;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 5px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .2s;
        }

        .btn-accept { background: var(--green); }
        .btn-reject { background: var(--red);   }
        .btn:hover  { opacity: .85; }

        .no-data { text-align: center; padding: 40px; color: #999; }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { display: none; }
            td { padding: 10px 15px; border: none; border-bottom: 1px solid #eee; }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>📦 E-Waste Pickup Requests</h1>
        <a href="admin.php" class="back-btn">← Dashboard</a>
    </header>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?>"><?= htmlspecialchars($message['text']) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Requested By</th>
                <th>Date &amp; Time</th>
                <th>Location</th>
                <th>E-Waste</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?> &times; <?= (int)$row['quantity'] ?></td>
                        <td><span class="badge badge-pending">Pending</span></td>
                        <td class="actions">
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?= (int)$row['id'] ?>">
                                <input type="hidden" name="action" value="accepted">
                                <button type="submit" class="btn btn-accept">✔ Accept</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?= (int)$row['id'] ?>">
                                <input type="hidden" name="action" value="rejected">
                                <button type="submit" class="btn btn-reject">✖ Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="no-data">🎉 No pending pickup requests.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
