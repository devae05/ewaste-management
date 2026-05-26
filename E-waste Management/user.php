<?php
session_start();
require_once 'auth_check.php';
require_login();

// Admins who accidentally land here get redirected
if ($_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard — E-Waste Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .bounce-animation { animation: bounce 2s ease infinite; }

        .fun-button {
            transition: all 0.3s ease-in-out;
        }

        .fun-button:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.45);
        }
    </style>
</head>
<body class="bg-gradient-to-b from-pink-100 to-yellow-100 min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-gradient-to-r from-yellow-300 to-pink-300 shadow-xl p-5 flex items-center justify-between rounded-3xl mx-4 mt-4">
        <div class="flex items-center space-x-4">
            <span class="text-5xl bounce-animation">♻️</span>
            <span class="text-4xl font-extrabold text-purple-700">E-WASTE</span>
        </div>
        <div class="flex items-center space-x-4 text-sm font-semibold text-gray-700">
            <span>Welcome back!</span>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-600 shadow-md px-8 py-4 flex justify-center space-x-10 text-white font-bold rounded-2xl mx-4 mt-5">
        <a href="user.php"    class="text-lg hover:text-yellow-300 transition-all duration-200 transform hover:scale-105">🏠 Home</a>
        <a href="Schedule.php" class="text-lg hover:text-yellow-300 transition-all duration-200 transform hover:scale-105">📅 Schedule Pickup</a>
        <a href="booking.php"  class="text-lg hover:text-yellow-300 transition-all duration-200 transform hover:scale-105">📦 My Bookings</a>
        <a href="history.php"  class="text-lg hover:text-yellow-300 transition-all duration-200 transform hover:scale-105">📋 History</a>
        <a href="logout.php"   class="text-lg hover:text-red-300 transition-all duration-200 transform hover:scale-105">🚪 Logout</a>
    </nav>

    <!-- Hero Section -->
    <main class="flex items-center justify-center min-h-[72vh] mx-4 mt-8">
        <div class="text-center max-w-3xl">
            <h1 class="text-5xl font-extrabold text-gray-800 mb-6 leading-tight">
                Welcome to the
                <span class="text-green-600">E-Waste Management System</span>
            </h1>
            <p class="text-xl text-gray-600 mt-4 mb-8 leading-relaxed">
                Recycle your old electronics responsibly. Schedule pickups, track your bookings,
                and join us in making the world greener and cleaner!
            </p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="Schedule.php"
                   class="fun-button bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-3 px-10 rounded-full shadow-xl text-lg font-semibold">
                    📅 Schedule a Pickup
                </a>
                <a href="booking.php"
                   class="fun-button bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 px-10 rounded-full shadow-xl text-lg font-semibold">
                    📦 View My Bookings
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-pink-500 to-purple-600 p-4 text-center mt-12 rounded-t-xl shadow-lg">
        <p class="text-sm text-white">&copy; 2025 E-Waste Management System. All rights reserved.</p>
    </footer>

</body>
</html>
