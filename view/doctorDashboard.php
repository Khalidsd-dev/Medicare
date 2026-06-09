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
                        <i class='bx bxs-folder-open' ></i>
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
                        <p>Review pending requests, manage confirmed appointments, and view your patient list.</p>

                        <div class="appointments-columns">
                            <!-- Pending Appointments Column -->
                            <div class="appointments-column">
                                <div class="column-header pending">
                                    <span class="column-icon"><i class='bx bx-time-five'></i></span>
                                    <h3>Pending Requests</h3>
                                    <span class="column-count" id="pending-count">0</span>
                                </div>
                                <div class="column-body doctor-appointments">
                                    <!-- Pending appointment cards go here -->
                                </div>
                            </div>

                            <!-- Approved Appointments Column -->
                            <div class="appointments-column">
                                <div class="column-header approved">
                                    <span class="column-icon"><i class='bx bx-check-circle'></i></span>
                                    <h3>Confirmed</h3>
                                    <span class="column-count" id="approved-count">0</span>
                                </div>
                                <div class="column-body approved-appointments">
                                    <!-- Approved appointment cards go here -->
                                </div>
                            </div>

                            <!-- Patient List Column -->
                            <div class="appointments-column">
                                <div class="column-header patients">
                                    <span class="column-icon"><i class='bx bx-group'></i></span>
                                    <h3>Patients</h3>
                                    <span class="column-count" id="patients-count">0</span>
                                </div>
                                <div class="column-body patient-list-content">
                                    <!-- Patient cards go here -->
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 40px 0;">

                        <!-- Schedule Management (keep as-is or below the columns) -->
                        <div class="appointments">
                            <h3>Schedule Management</h3>
                            <form id="appointment-status-form" class="status-management-form">
                                <label for="status-appointment-select">Select appointment</label>
                                <select id="status-appointment-select" required>
                                    <option value="">Choose appointment</option>
                                </select>

                                <label for="appointment-status">Set status</label>
                                <select id="appointment-status" required>
                                    <option value="">Choose status</option>
                                    <option value="CONFIRMED">Confirm</option>
                                    <option value="COMPLETED">Complete</option>
                                    <option value="CANCELLED">Cancel</option>
                                </select>

                                <button class="book-btn" type="submit">Update Status</button>
                                <p id="status-update-feedback" class="booking-feedback"></p>
                            </form>
                        </div>
                    </div>

                    <div id="route-medical-records" class="route-page hidden">
                        <h2>Medical Records</h2>
                        <p>Fill in medical details for a confirmed appointment, then save to complete the appointment.</p>
                        <hr>
                        <div class="medical-records-panel">
                            <div class="medical-records-header">
                                <div>
                                    <h3>Doctor record entry</h3>
                                    <p>Use this section to store diagnosis, prescription, and treatment notes for each appointment.</p>
                                </div>
                                <span class="status-pill">Confirmed appointments only</span>
                            </div>

                            <div class="medical-records-grid">
                                <form id="medical-record-form" class="medical-records-form">
                                    <div class="medical-records-row full-width">
                                        <label for="medical-record-appointment">Select appointment</label>
                                        <select id="medical-record-appointment" required>
                                            <option value="">Select a confirmed appointment</option>
                                        </select>
                                    </div>

                                    <div class="medical-records-row">
                                        <label for="medical-record-diagnosis">Diagnosis</label>
                                        <textarea id="medical-record-diagnosis" rows="4" placeholder="Enter diagnosis" required></textarea>
                                    </div>

                                    <div class="medical-records-row">
                                        <label for="medical-record-prescription">Prescription</label>
                                        <textarea id="medical-record-prescription" rows="3" placeholder="Enter prescription details" required></textarea>
                                    </div>

                                    <div class="medical-records-row full-width">
                                        <label for="medical-record-treatment">Treatment Notes</label>
                                        <textarea id="medical-record-treatment" rows="4" placeholder="Enter treatment notes" required></textarea>
                                    </div>

                                    <div class="form-actions">
                                        <button class="book-btn" type="submit">Save Medical Record & Complete Appointment</button>
                                    </div>
                                </form>

                                <aside class="medical-records-sidebar">
                                    <div class="info-card">
                                        <h4>Tips for accurate records</h4>
                                        <p>Keep notes clear and concise. Record the main diagnosis, prescribed treatment, and follow-up instructions.</p>
                                    </div>
                                    <div class="info-card">
                                        <h4>Why it matters</h4>
                                        <p>Well-maintained records improve patient follow-up and support structured care across the team.</p>
                                    </div>
                                </aside>
                            </div>

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
