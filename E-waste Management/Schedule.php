<?php
session_start();
require_once 'auth_check.php';
require_login();
include 'db.php';

$message  = '';
$user_id  = (int) $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date     = $_POST['date']     ?? '';
    $time     = $_POST['time']     ?? '';
    $location = trim($_POST['location'] ?? '');
    $type     = $_POST['type']     ?? '';
    $quantity = (int) ($_POST['quantity'] ?? 0);

    // Basic server-side validation
    if (!$date || !$time || !$location || !$type || $quantity < 1) {
        $message = ['type' => 'error', 'text' => 'Please fill in all fields correctly.'];
    } elseif (strtotime($date) < strtotime('today')) {
        $message = ['type' => 'error', 'text' => 'Please select a future date.'];
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO ewaste_booking (user_id, date, time, location, type, quantity)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issssi", $user_id, $date, $time, $location, $type, $quantity);
        if ($stmt->execute()) {
            $message = ['type' => 'success', 'text' => 'Your pickup has been scheduled successfully!'];
        } else {
            $message = ['type' => 'error', 'text' => 'Could not save booking. Please try again.'];
        }
        $stmt->close();
    }
}

$allowed_types = ['Laptop','Mobile Phone','Tablet','Charger','Earphones','Keyboard','Mouse','Monitor','CPU','Printer','Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Pickup — E-Waste Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #34c759;
            --primary-dark: #2aa44f;
            --red: #ff3b30;
            --border: #e0e0e0;
            --shadow: rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        .top-nav {
            width: 100%;
            max-width: 560px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .top-nav a {
            color: #555;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            background: #fff;
            padding: 7px 14px;
            border-radius: 20px;
            box-shadow: 0 2px 6px var(--shadow);
            transition: background .2s;
        }

        .top-nav a:hover { background: #f0f0f0; }

        .card {
            background: #fff;
            max-width: 560px;
            width: 100%;
            padding: 38px;
            border-radius: 14px;
            box-shadow: 0 12px 32px var(--shadow);
        }

        .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .card-header .icon { font-size: 48px; color: var(--primary); }

        .card-header h2 {
            font-size: 26px;
            color: #2c3e50;
            margin: 10px 0 6px;
        }

        .card-header p { color: #7f8c8d; font-size: 14px; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        .alert-success { background: rgba(52,199,89,.15);  color: var(--primary-dark); }
        .alert-error   { background: rgba(255,59,48,.12);  color: var(--red); }

        .form-row { display: flex; gap: 16px; }
        .form-row .form-group { flex: 1; }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }

        input, select {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52,199,89,.2);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23555' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: background .2s, transform .1s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover  { background: var(--primary-dark); }
        .btn-submit:active { transform: scale(.98); }

        .eco-note {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: var(--primary-dark);
        }

        @media (max-width: 480px) {
            .form-row { flex-direction: column; gap: 0; }
            .card { padding: 26px; }
        }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="user.php">← Back to Home</a>
    <a href="booking.php">My Bookings</a>
</div>

<div class="card">
    <div class="card-header">
        <div class="icon"><i class="fas fa-recycle"></i></div>
        <h2>Schedule E-Waste Pickup</h2>
        <p>Responsibly recycle your electronics — schedule a free pickup below.</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?>"><?= htmlspecialchars($message['text']) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="date"><i class="far fa-calendar-alt"></i> Date</label>
                <input type="date" id="date" name="date"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </div>
            <div class="form-group">
                <label for="time"><i class="far fa-clock"></i> Time Slot</label>
                <input type="time" id="time" name="time" required>
            </div>
        </div>

        <div class="form-group">
            <label for="location"><i class="fas fa-map-marker-alt"></i> Pickup Location</label>
            <input type="text" id="location" name="location" placeholder="Enter your full address" required>
        </div>

        <div class="form-group">
            <label for="type"><i class="fas fa-laptop"></i> Type of E-Waste</label>
            <select id="type" name="type" required>
                <option value="">— Select Type —</option>
                <?php foreach ($allowed_types as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity"><i class="fas fa-cubes"></i> Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" max="100"
                   placeholder="Number of items" required>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-check-circle"></i> Schedule Pickup
        </button>
    </form>

    <p class="eco-note">♻️ Thank you for helping keep the planet clean!</p>
</div>

</body>
</html>
