<?php
session_start();

// Ensure only doctors can access this page
if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'DOCTOR') {
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
    <title>Medicare | Doctor Messages</title>
</head>
<body>

    <div class="main-container">

        <div class="dashboard-container">
            <div class="side-panel">
                <div class="avatar-sec">
                    <div class="avatar-icon">
                        <span class="avatar-icon-img" id="initials"><?php echo htmlspecialchars($_SESSION['USER_INITIALS'] ?? ''); ?></span>
                    </div>
                    <div class="avatar-name">
                        <h3 id="user_name"><?php echo htmlspecialchars($_SESSION['USER_NAME'] ?? ''); ?></h3>
                    </div>
                    <div class="avatar-id">
                        <p id="user_id">ID: <?php echo htmlspecialchars($_SESSION['USER_ID'] ?? ''); ?></p>
                    </div>
                </div>
                
                <div class="side-links">
                    <div class="side-link">
                        <i class='bx bxs-dashboard'></i>
                        <a href="doctorDashboard.php">Dashboard</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-calendar'></i>
                        <a href="doctorAppointments.php">Appointments</a>
                        <div class="counter">
                            <span class="appt-count">0</span>
                        </div>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-file-medical'></i>
                        <a href="doctorMedicalRecords.php">Medical Records</a>
                    </div>
                    <div class="side-link active">
                        <i class='bx bxs-message'></i>
                        <a href="doctorMessages.php">Messages</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bx-log-out'></i>
                        <a href="../php/logout.php">Log Out</a>
                    </div>
                </div>

            </div>
            <div class="contents">
                <div class="contents-container">
                    <div class="route-page">
                        <h2>Messages</h2>
                        <p>Message management is coming soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
