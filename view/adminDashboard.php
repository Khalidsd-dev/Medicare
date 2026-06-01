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
                        <a href="adminAppointments.php" class="route-link" data-route="appointments">Appointments</a>
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
                    <!-- Dashboard Route -->
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

                    <!-- Appointments Route -->
                    <div id="route-appointments" class="route-page hidden">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Management</p>
                                <h1>Appointments</h1>
                            </div>
                        </div>
                        <p class="section-description">View and manage all system appointments.</p>
                        <div id="admin-appointments-container">
                            <p>Loading appointments...</p>
                        </div>
                    </div>

                    <!-- User Access Route -->
                    <div id="route-user-access" class="route-page hidden">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Management</p>
                                <h1>User Access Control</h1>
                            </div>
                            <div class="heading-actions">
                                <button class="secondary-button" id="toggle-user-form-btn">Create User</button>
                            </div>
                        </div>
                        <p class="section-description">Manage user accounts and access permissions.</p>

                        <!-- Create User Form -->
                        <div id="admin-user-form-container" style="display: none;">
                            <form id="admin-user-create-form" class="form-container">
                                <h3>Create New User</h3>
                                <div class="form-group">
                                    <label for="admin-first-name">First Name</label>
                                    <input type="text" id="admin-first-name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="admin-last-name">Last Name</label>
                                    <input type="text" id="admin-last-name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="admin-email">Email</label>
                                    <input type="email" id="admin-email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="admin-password">Password</label>
                                    <input type="password" id="admin-password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="admin-gender">Gender</label>
                                    <select id="admin-gender" name="gender">
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="admin-role">Role</label>
                                    <select id="admin-role" name="role">
                                        <option value="PATIENT">Patient</option>
                                        <option value="DOCTOR">Doctor</option>
                                        <option value="ADMIN">Admin</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="admin-account-status">Account Status</label>
                                    <select id="admin-account-status" name="account_status">
                                        <option value="ACTIVE">Active</option>
                                        <option value="INACTIVE">Inactive</option>
                                        <option value="SUSPENDED">Suspended</option>
                                    </select>
                                </div>
                                <div id="admin-create-user-feedback" style="margin: 10px 0;"></div>
                                <button type="submit" class="primary-button">Create User</button>
                            </form>
                        </div>

                        <!-- User Access Table -->
                        <div id="admin-user-access-table">
                            <p>Loading user accounts...</p>
                        </div>
                    </div>

                    <!-- Settings Route -->
                    <div id="route-settings" class="route-page hidden">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Preferences</p>
                                <h1>System Settings</h1>
                            </div>
                        </div>
                        <p class="section-description">Configure system appearance and preferences.</p>
                        
                        <div class="settings-section">
                            <h3>Theme Settings</h3>
                            <div class="form-group">
                                <label for="theme-select">Theme</label>
                                <select id="theme-select">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="accent-color">Accent Color</label>
                                <input type="color" id="accent-color" value="#007bff">
                            </div>
                            <div class="form-group">
                                <label for="card-radius">Card Border Radius</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="range" id="card-radius" min="0" max="20" value="8">
                                    <span id="card-radius-value">8px</span>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button class="primary-button" id="apply-theme-btn">Apply Settings</button>
                                <button class="secondary-button" id="reset-theme-btn">Reset to Default</button>
                            </div>
                        </div>

                        <div id="settings-list">
                            <p>Loading settings...</p>
                        </div>
                    </div>

                    <!-- Messages Route -->
                    <div id="route-messages" class="route-page hidden">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Communication</p>
                                <h1>Messages</h1>
                            </div>
                        </div>
                        <p class="section-description">View system notifications and messages.</p>
                        <div id="messages-container">
                            <p>No messages at this time.</p>
                        </div>
                    </div>
                    
                </div>
    </div>
    </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>