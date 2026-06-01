<?php
session_start();
// Ensure only admins can access this page
if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'ADMIN') {
    header('Location: ../php/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Medicare | ADMIN DASHBOARD</title>
</head>
<body>
    <div class="main-container">

    <div class="dashboard-container">
        <div class="side-panel">
            <div class="avatar-sec">
                <div class="avatar-icon">
                    <span class="avatar-icon-img" id="initials"></span>
                </div>
                <div class="avatar-name">
                    <h3 id="user_name"><?php echo htmlspecialchars($_SESSION['USER_NAME'] ?? '') ?></h3>
                </div>
                <div class="avatar-id">
                    <p id="user_id"><?php echo htmlspecialchars($_SESSION['USER_ID'] ?? '') ?></p>
                </div>
            </div>

            <div class="side-links">
                <div class="side-link">
                        <i class='bx bxs-dashboard'></i>
                        <a href="#" class="route-link" data-route="dashboard">Dashboard</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-calendar'></i>
                        <a href="#" class="route-link" data-route="appointments">Appointments</a>
                        <div class="counter">
                            <span class="appt-count">0</span>
                        </div>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-user-account'></i>
                        <a href="#" class="route-link" data-route="user-access">User Access</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-message'></i>
                        <a href="#" class="route-link" data-route="messages">Messages</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bx-cog'></i>
                        <a href="#" class="route-link" data-route="settings">Settings</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bx-log-out'></i>
                        <a href="../php/logout.php">Log Out</a>
                    </div>
            </div>

        </div>

        <div class="contents">
                <div class="contents-container">
                    <div id="route-appointments" class="route-page hidden">
                        <h2 class="admin-title">Appointment Management</h2>
                        <p>Review and manage appointment requests, confirmations, and scheduling at a system level.</p>
                        <hr>
                        <div class="admin-panel">
                            <p>Use this section to keep appointments organized and to ensure every request is handled properly.</p>
                        </div>
                    </div>
                </div>
    </div>
    </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>