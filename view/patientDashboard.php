<?php
session_start();
// Ensure only patients can access
if (!isset($_SESSION['LOGGED_IN_USER']) || $_SESSION['LOGGED_IN_USER'] !== true || ($_SESSION['USER_ROLE'] ?? '') !== 'PATIENT') {
    header('Location: ../php/login.php');
    exit;
}

require_once __DIR__ . '/../php/Executor.php';

$executor = new Executor();
$appointments = [];
try {
    $appointments = $executor->databaseManager->viewAppointments($_SESSION['USER_ID']);
    if (!$appointments) $appointments = [];
} catch (Exception $e) {
    $appointments = [];
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
    <title>MediCare | Patient Dashboard</title>
</head>
<body>
    <div class="main-container">
        <div class="dashboard">
            <div class="dashboard-container">
                <div class="side-panel">
                <div class="avatar-sec">
                    <div class="avatar-icon">
                        <div class="avatar-icon-img">
                           <p id="initials"><?php echo htmlspecialchars($_SESSION['USER_INITIALS'] ?? ''); ?></p>
                        </div>
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
                        <a href="patientDashboard.php">Dashboard</a>
                    </div>

                    <div class="side-link">
                        <i class='bx bx-task'></i>
                        <a href="dashboardAdmin.html">Appointments</a>
                            <div class="counter">
                                <span class="appt-count">3</span>
                            </div>
                    </div>

                    <div class="side-link">
                        <i class='bx bx-log-out'></i>
                        <a href="../php/logout.php">Log Out</a>
                    </div>
                </div>
                </div>


                <!--- Contents Panel-->
                <div class="contents">
                    <div class="contents-container">
                            <h2>Book an appointment</h2>
                            <p>Select a doctor and choose your preferred time</p>
                        <div class="apt-container">
                            <h3>Available Doctors</h3>
                            <div class="apt" id="doctors-list">
                                <!-- Doctor cards will be loaded here from the server -->
                            </div>
                        </div>
                        <!-- End of Appointment Container-->

                            <!-- Begining of Schedule Details-->
                            <div class="schedule-details">
                                <h3>Schedule Details</h3>
                                <p>Here you can schedule your appointment at any time that suits you.</p>

                                <div class="selected-doctor-details" id="selected-doctor-details">
                                Select a doctor above to proceed.
                            </div>
                            <div class="fields">
                                <div class="fields-container">
                                    <label for="date">Date</label>
                                    <input type="date" id="date">
                                </div>

                                <div class="fields-container">
                                    <label for="time">Time</label>
                                    <input type="time" id="time">
                                </div>

                                <div class="fields-container">
                                    <button class="book-btn" id="confirm-book-btn" type="button" disabled>Book now</button>
                                </div>
                            </div>
                            <p class="booking-feedback" id="booking-feedback"></p>
                        </div>
                        <!-- End of Schedule Details -->

                            <div class="appointments">
                                <h3>Scheduled Appoinments</h3>

                            <div class="scheduled-appointments">
                                <p>No booked appointments</p>
                            </div>
                        </div>
                        </div>

                        
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script>
        // Expose initial appointments to client-side script for immediate rendering
        window.INITIAL_APPOINTMENTS = <?php echo json_encode(array_values($appointments)); ?>;
    </script>
    <script src="../js/script.js"></script>
</body>
</html>
