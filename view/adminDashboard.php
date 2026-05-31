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
                    <div id="route-dashboard" class="route-page">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Overview</p>
                                <h1>Admin Control Center</h1>
                            </div>
                            <div class="heading-actions">
                                <button class="secondary-button" id="refresh-admin-data">Refresh data</button>
                            </div>
                        </div>
                        <p class="section-description">Monitor system health, appointment activity, and user access in one place.</p>
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
                            <div class="summary-card">
                                <h4>Theme</h4>
                                <p class="summary-value" id="current-theme">Light</p>
                            </div>
                        </div>
                    </div>
                    <div id="route-appointments" class="route-page hidden">
                        <h2 class="admin-title">Appointment Management</h2>
                        <p>Review and manage appointment requests, confirmations, and scheduling at a system level.</p>
                        <hr>
                        <div class="admin-panel">
                            <p>Use this section to keep appointments organized and to ensure every request is handled properly.</p>
                        </div>
                    </div>
                    <div id="route-user-access" class="route-page hidden">
                        <h2 class="admin-title">User Access</h2>
                        <p>Review and manage access for patients, doctors, and administrators.</p>
                        <hr>
                        <div class="dashboard-summary">
                            <div class="summary-card">
                                <h4>Active Patients</h4>
                                <p class="summary-value" id="user-count-patients">0</p>
                            </div>
                            <div class="summary-card">
                                <h4>Active Doctors</h4>
                                <p class="summary-value" id="user-count-doctors">0</p>
                            </div>
                            <div class="summary-card">
                                <h4>Admin Accounts</h4>
                                <p class="summary-value" id="user-count-admins">0</p>
                            </div>
                        </div>
                        <div class="admin-panel">
                            <h3>User Accounts</h3>
                            <div id="admin-user-access-table" class="user-table-container">
                                <p>Loading user access data...</p>
                            </div>
                        </div>
                    </div>
                    <div id="route-settings" class="route-page hidden">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Settings</p>
                                <h2>Dashboard Theme</h2>
                            </div>
                        </div>
                        <p class="section-description">Choose a visual theme and have it apply across the admin dashboard immediately.</p>
                        <div class="admin-panel settings-panel">
                            <form id="admin-theme-form">
                                <div class="form-row">
                                    <label for="theme-select">Theme</label>
                                    <select id="theme-select" name="theme">
                                        <option value="light">Light</option>
                                        <option value="dark">Dark</option>
                                        <option value="blue">Blue</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label for="accent-color">Accent color</label>
                                    <input type="color" id="accent-color" name="accentColor" value="#4F46E5">
                                </div>
                                <div class="form-row range-row">
                                    <label for="card-radius">Card border radius</label>
                                    <div class="range-group">
                                        <input type="range" id="card-radius" name="cardRadius" min="0" max="32" value="16">
                                        <span class="range-value" id="card-radius-value">16px</span>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="primary-button" id="apply-theme-btn">Apply theme</button>
                                    <button type="button" class="secondary-button" id="reset-theme-btn">Reset defaults</button>
                                </div>
                            </form>
                        </div>
                        <div class="admin-panel">
                            <h3>Saved configuration</h3>
                            <ul id="settings-list" class="settings-list">
                                <li>Loading settings...</li>
                            </ul>
                        </div>
                    </div>
                    <div id="route-messages" class="route-page hidden">
                        <h2 class="admin-title">Messages</h2>
                        <p>Communications, alerts, and notifications for administrators will be displayed in this section.</p>
                        <hr>
                        <div class="admin-panel">
                            <p>Monitor system notifications or incoming messages from users and doctors.</p>
                        </div>
                    </div>
                </div>
    </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>