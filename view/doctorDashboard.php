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
    <style>
        .hidden { display: none; }
        .dashboard-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-top: 1rem; }
        .summary-card { background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .summary-card h4 { margin-bottom: 0.75rem; }
        .summary-value { font-size: 2rem; font-weight: 700; }
    </style>
    <title>Medicare | Doctor Dashboard</title>
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
                        <i class='bx bxs-file-medical'></i>
                        <a href="#" class="route-link" data-route="medical-records">Medical Records</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bxs-message'></i>
                        <a href="#" class="route-link" data-route="messages">Messages</a>
                    </div>
                    <div class="side-link">
                        <i class='bx bx-log-out'></i>
                        <a href="../php/logout.php">Log Out</a>
                    </div>
                </div>
            </div>
            <div class="contents">
                <div class="contents-container">
                    <div id="route-dashboard" class="route-page">
                        <h2>Doctor Dashboard</h2>
                        <p>Welcome to your dashboard doctor!</p>
                        <p>Use the left navigation to manage appointments, view medical records, and keep your patient care organized.</p>
                        <hr>
                        <div class="dashboard-summary">
                            <div class="summary-card">
                                <h4>Pending Requests</h4>
                                <p class="summary-value" id="summary-pending">0</p>
                            </div>
                            <div class="summary-card">
                                <h4>Confirmed Appointments</h4>
                                <p class="summary-value" id="summary-confirmed">0</p>
                            </div>
                            <div class="summary-card">
                                <h4>Completed</h4>
                                <p class="summary-value" id="summary-completed">0</p>
                            </div>
                        </div>
                    </div>

                    <div id="route-appointments" class="route-page hidden">
                        <h2>Appointment Management</h2>
                        <hr>
                        <div class="search-area">
                            <div class="search-bar">
                                <i class='bx bx-search'></i>
                                <input class="searchbar" type="text" placeholder="Search patients...">
                                <button class="search-btn" type="button">Search</button>
                            </div>

                            <div class="filter">
                                <i class='bx bx-filter-alt'></i>
                                <select class="filter-options">
                                    <option value="">Filter by</option>
                                    <option value="name">Name</option>
                                    <option value="id">Patient ID</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="date">Appointment Date</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="appointments">
                            <h3>Pending Appointments</h3>
                            <div class="doctor-appointments"></div>
                        </div>

                        <div class="appointments" id="approved-appointments">
                            <h3>Approved Appointments</h3>
                            <div class="approved-appointments"></div>
                        </div>

                        <hr>

                        <div class="patient-list">
                            <h3>Patient List</h3>
                            <div class="patient-list-content"></div>
                        </div>
                    </div>

                    <div id="route-medical-records" class="route-page hidden">
                        <h2>Medical Records</h2>
                        <p>Fill in medical details for a confirmed appointment, then save to complete the appointment.</p>
                        <hr>
                        <div class="medical-records-panel">
                            <form id="medical-record-form">
                                <label for="medical-record-appointment">Select appointment</label>
                                <select id="medical-record-appointment" required>
                                    <option value="">Select a confirmed appointment</option>
                                </select>

                                <label for="medical-record-diagnosis">Diagnosis</label>
                                <textarea id="medical-record-diagnosis" rows="4" placeholder="Enter diagnosis"></textarea>

                                <label for="medical-record-prescription">Prescription</label>
                                <textarea id="medical-record-prescription" rows="3" placeholder="Enter prescription details"></textarea>

                                <label for="medical-record-treatment">Treatment Notes</label>
                                <textarea id="medical-record-treatment" rows="4" placeholder="Enter treatment notes"></textarea>

                                <button class="book-btn" type="submit">Save Medical Record & Complete Appointment</button>
                            </form>
                            <div id="medical-record-feedback" class="booking-feedback"></div>
                        </div>
                    </div>

                    <div id="route-messages" class="route-page hidden">
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
