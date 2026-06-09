// Run script after DOM loads
document.addEventListener('DOMContentLoaded', function () {

    const userName = document.getElementById('user_name');
    const userId = document.getElementById('user_id');
    const userInitials = document.getElementById('initials');

    let doctorCards = [];

    const doctorsContainer = document.getElementById('doctors-list');
    const selectedDoctorDetails = document.getElementById('selected-doctor-details');
    const confirmBookBtn = document.getElementById('confirm-book-btn');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const bookingFeedback = document.getElementById('booking-feedback');
    const scheduledAppointments = document.querySelector('.scheduled-appointments');
    const doctorAppointments = document.querySelector('.doctor-appointments');
    const approvedAppointments = document.querySelector('.approved-appointments');
    const patientList = document.querySelector('.patient-list-content');
    const appointmentCountLabel = document.querySelector('.appt-count');
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.searchbar');
    const filterOptions = document.querySelector('.filter-options');
    const bookingForm = document.getElementById('booking-form');
    const selectedDoctorIdInput = document.getElementById('selected-doctor-id');
    const medicalRecordForm = document.getElementById('medical-record-form');
    const medicalRecordAppointmentSelect = document.getElementById('medical-record-appointment');
    const medicalRecordDiagnosis = document.getElementById('medical-record-diagnosis');
    const medicalRecordPrescription = document.getElementById('medical-record-prescription');
    const medicalRecordTreatment = document.getElementById('medical-record-treatment');
    const medicalRecordFeedback = document.getElementById('medical-record-feedback');
    const appointmentStatusForm = document.getElementById('appointment-status-form');
    const statusAppointmentSelect = document.getElementById('status-appointment-select');
    const appointmentStatusSelect = document.getElementById('appointment-status');
    const statusUpdateFeedback = document.getElementById('status-update-feedback');
    const adminUserCreateForm = document.getElementById('admin-user-create-form');
    const adminCreateUserFeedback = document.getElementById('admin-create-user-feedback');
    const routeLinks = document.querySelectorAll('.route-link');

 // Route to pages
    const routePages = {
        dashboard: document.getElementById('route-dashboard'),
        appointments: document.getElementById('route-appointments'),
        'user-access': document.getElementById('route-user-access'),
        'medical-records': document.getElementById('route-medical-records'),
        settings: document.getElementById('route-settings'),
        messages: document.getElementById('route-messages')
    };

    const summaryPending = document.getElementById('summary-pending');
    const summaryConfirmed = document.getElementById('summary-confirmed');
    const summaryCompleted = document.getElementById('summary-completed');
    const userCountPatients = document.getElementById('user-count-patients');
    const userCountDoctors = document.getElementById('user-count-doctors');
    const userCountAdmins = document.getElementById('user-count-admins');
    const adminUserAccessTable = document.getElementById('admin-user-access-table');
    const settingsList = document.getElementById('settings-list');
    const themeSelect = document.getElementById('theme-select');
    const accentColorInput = document.getElementById('accent-color');
    const cardRadiusInput = document.getElementById('card-radius');
    const cardRadiusValue = document.getElementById('card-radius-value');
    const applyThemeBtn = document.getElementById('apply-theme-btn');
    const resetThemeBtn = document.getElementById('reset-theme-btn');
    const currentThemeLabel = document.getElementById('current-theme');

    const isDoctorPage = window.location.pathname.includes('doctor');
    const isAdminPage = window.location.pathname.includes('adminDashboard');
    const isPatientPage = window.location.pathname.includes('patientDashboard');
    let selectedDoctor = null;
    let doctorAppointmentCache = [];

    fetchUserData();

    if (isPatientPage) {
        fetchDoctors();

        if (bookingForm) {
            bookingForm.addEventListener('submit', handleBookNow);
        }

        if (window.INITIAL_APPOINTMENTS && Array.isArray(window.INITIAL_APPOINTMENTS) && window.INITIAL_APPOINTMENTS.length) {
            renderScheduledAppointments(window.INITIAL_APPOINTMENTS);
        } else {
            fetchAppointments();
        }
    }

    if (isDoctorPage) {
        if (appointmentStatusForm) {
            appointmentStatusForm.addEventListener('submit', handleAppointmentStatusSubmit);
        }
    }

    if (isAdminPage) {
        if (adminUserCreateForm) {
            adminUserCreateForm.addEventListener('submit', handleAdminUserCreateSubmit);
        }
        
        const toggleUserFormBtn = document.getElementById('toggle-user-form-btn');
        const userFormContainer = document.getElementById('admin-user-form-container');
        if (toggleUserFormBtn && userFormContainer) {
            toggleUserFormBtn.addEventListener('click', () => {
                userFormContainer.style.display = userFormContainer.style.display === 'none' ? 'block' : 'none';
            });
        }

        const refreshAdminDataBtn = document.getElementById('refresh-admin-data');
        if (refreshAdminDataBtn) {
            refreshAdminDataBtn.addEventListener('click', () => {
                fetchAdminOverview();
            });
        }
    }

    if (isDoctorPage || isAdminPage || isPatientPage) {
        if (routeLinks.length > 0) {
            setupRouteNavigation();
        }
    }

    if (isAdminPage) {
        setupThemeControls();
        loadStoredTheme();
    }

    if (isDoctorPage && doctorAppointments) {
        fetchDoctorAppointments();

        if (searchBtn) {
            searchBtn.addEventListener('click', handleDoctorSearch);
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', (event) => {
                if (event.key === 'Enter') {
                    handleDoctorSearch();
                }
            });
        }

        if (filterOptions) {
            filterOptions.addEventListener('change', handleDoctorSearch);
        }

        if (medicalRecordForm) {
            medicalRecordForm.addEventListener('submit', handleMedicalRecordSubmit);
        }
    }

    function setupDoctorSelection() {
        doctorCards.forEach(card => {
            const button = card.querySelector('.select-doctor-btn');
            if (!button) return;

            button.addEventListener('click', () => {
                selectedDoctor = {
                    id: card.dataset.doctorId,
                    name: card.dataset.doctorName,
                    specialty: card.dataset.doctorSpecialty
                };

                doctorCards.forEach(c => c.classList.toggle('selected', c === card));
                updateSelectedDoctorDetails();
                clearFeedback();
            });
        });
    }

    function fetchDoctors() {
        if (!doctorsContainer) return;

        fetch('../php/getDoctors.php')
            .then(response => response.json())
            .then(data => {
                const doctors = data.doctors || [];
                if (!doctors.length) {
                    doctorsContainer.innerHTML = '<p>No doctors available.</p>';
                    return;
                }

                doctorsContainer.innerHTML = doctors.map(d => {
                    const name = `${d.first_name ? d.first_name + ' ' : ''}${d.last_name || ''}`.trim() || d.doctor_name || `Doctor #${d.doctor_id}`;
                    const specialty = d.specialization || d.doctor_specialty || '';
                    return `
                        <div class="apt-card" data-doctor-id="${d.doctor_id}" data-doctor-name="${name}" data-doctor-specialty="${specialty}">
                            <h4 class="doctor-name">${name}</h4>
                            <p class="doctor-job">${specialty}</p>
                            <div class="visit-time">
                                <p class="visit-days">Mon-Wed</p>
                            </div>
                            <button class="book-btn select-doctor-btn" type="button">Select</button>
                        </div>
                    `;
                }).join('');

                doctorCards = doctorsContainer.querySelectorAll('.apt-card');
                setupDoctorSelection();
            })
            .catch(err => {
                console.error('Error loading doctors:', err);
                doctorsContainer.innerHTML = '<p>Unable to load doctors.</p>';
            });
    }

    function updateSelectedDoctorDetails() {
        if (!selectedDoctor) {
            selectedDoctorDetails.textContent = 'Select a doctor above to proceed.';
            if (selectedDoctorIdInput) {
                selectedDoctorIdInput.value = '';
            }
            if (confirmBookBtn) {
                confirmBookBtn.disabled = true;
            }
            return;
        }

        selectedDoctorDetails.innerHTML = `
            <strong>Doctor selected:</strong> ${escapeHtml(selectedDoctor.name)} (${escapeHtml(selectedDoctor.specialty)})
        `;

        if (selectedDoctorIdInput) {
            selectedDoctorIdInput.value = selectedDoctor.id;
        }

        if (confirmBookBtn) {
            confirmBookBtn.disabled = false;
        }
    }

    function handleBookNow() {
        clearFeedback();

        if (!selectedDoctor) {
            showFeedback('Please select a doctor before booking.', true);
            return;
        }

        const appointmentDate = dateInput.value;
        const appointmentTime = timeInput.value;

        if (!appointmentDate || !appointmentTime) {
            showFeedback('Please choose both date and time for your appointment.', true);
            return;
        }

        const bookingData = {
            doctor_id: selectedDoctor.id,
            doctor_name: selectedDoctor.name,
            doctor_specialty: selectedDoctor.specialty,
            appointment_date: appointmentDate,
            appointment_time: appointmentTime
        };

        fetch('../php/bookAppointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookingData)
        })
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    showFeedback(data.error || 'Unable to save appointment. Please try again.', true);
                    return;
                }

                showFeedback(data.message || 'Appointment booked successfully.');

                // If the API returned the created appointment, append it immediately
                if (data.appointment) {
                    appendAppointmentToDOM(data.appointment);
                } else {
                    // fallback: reload the appointments list from server
                    fetchAppointments();
                }
            })
            .catch(error => {
                console.error('Booking error:', error);
                showFeedback('A network error occurred while booking your appointment.', true);
            });
    }

    function fetchAppointments() {
        if (!scheduledAppointments) return;

        fetch('../php/getAppointments.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    scheduledAppointments.innerHTML = '<p>No booked appointments.</p>';
                    return;
                }

                renderScheduledAppointments(data.appointments || []);
            })
            .catch(error => {
                console.error('Error fetching appointments:', error);
                scheduledAppointments.innerHTML = '<p>Unable to load appointments.</p>';
            });
    }

    function fetchDoctorAppointments() {
        if (!doctorAppointments) return;

        fetch('../php/getDoctorAppointments.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    if (doctorAppointments) doctorAppointments.innerHTML = '<p>No appointment requests available.</p>';
                    if (approvedAppointments) approvedAppointments.innerHTML = '<p>No approved appointments yet.</p>';
                    if (patientList) patientList.innerHTML = '<p class="no-appointments">No patient data available.</p>';
                    updateApprovedAppointmentCount(0);
                    return;
                }

                doctorAppointmentCache = data.appointments || [];
                populateAppointmentStatusOptions(doctorAppointmentCache);
                renderDoctorAppointments(doctorAppointmentCache);
            })
            .catch(error => {
                console.error('Error fetching doctor appointments:', error);
                doctorAppointments.innerHTML = '<p>Unable to load appointments.</p>';
            });
    }

    function fetchAdminOverview() {
        fetch('../php/getAdminOverview.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    console.error('Unable to load admin overview:', data?.error);
                    return;
                }

                if (data.appointment_counts) {
                    if (summaryPending) summaryPending.textContent = (data.appointment_counts.REQUESTED || 0).toString();
                    if (summaryConfirmed) summaryConfirmed.textContent = (data.appointment_counts.CONFIRMED || 0).toString();
                    if (summaryCompleted) summaryCompleted.textContent = (data.appointment_counts.COMPLETED || 0).toString();
                }

                if (data.user_counts) {
                    if (userCountPatients) userCountPatients.textContent = (data.user_counts.PATIENT || 0).toString();
                    if (userCountDoctors) userCountDoctors.textContent = (data.user_counts.DOCTOR || 0).toString();
                    if (userCountAdmins) userCountAdmins.textContent = (data.user_counts.ADMIN || 0).toString();
                }
            })
            .catch(error => {
                console.error('Error fetching admin overview:', error);
            });
    }

    function fetchAdminUsers() {
        if (!adminUserAccessTable) return;

        adminUserAccessTable.innerHTML = '<p>Loading user access data...</p>';

        fetch('../php/getAdminUsers.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    adminUserAccessTable.innerHTML = '<p>Unable to load user access data.</p>';
                    console.error('Unable to load admin users:', data?.error);
                    return;
                }

                renderAdminUsersTable(data.users || []);
            })
            .catch(error => {
                console.error('Error fetching admin users:', error);
                adminUserAccessTable.innerHTML = '<p>Unable to load user access data.</p>';
            });
    }

    function renderAdminUsersTable(users) {
        if (!adminUserAccessTable) return;
        if (!users.length) {
            adminUserAccessTable.innerHTML = '<p>No user accounts found.</p>';
            return;
        }

        const rows = users.map(user => {
            const name = `${user.first_name || ''} ${user.last_name || ''}`.trim() || `User #${user.user_id}`;
            return `
                <tr>
                    <td>${escapeHtml(user.user_id)}</td>
                    <td>${escapeHtml(name)}</td>
                    <td>${escapeHtml(user.email || '—')}</td>
                    <td>${escapeHtml(user.user_role || '—')}</td>
                    <td>${escapeHtml(user.account_status || '—')}</td>
                    <td>${escapeHtml(user.created_at || '—')}</td>
                </tr>
            `;
        }).join('');

        adminUserAccessTable.innerHTML = `
            <div class="table-scroll">
                <table class="admin-user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    }

    function fetchAdminSettings() {
        if (!settingsList) return;

        settingsList.innerHTML = '<li>Loading settings...</li>';

        fetch('../php/getAdminSettings.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    settingsList.innerHTML = '<li>Unable to load settings.</li>';
                    console.error('Unable to load admin settings:', data?.error);
                    return;
                }

                renderAdminSettings(data.settings || {});
            })
            .catch(error => {
                console.error('Error fetching admin settings:', error);
                settingsList.innerHTML = '<li>Unable to load settings.</li>';
            });
    }

    function fetchAdminAppointments() {
        const appointmentsContainer = document.getElementById('admin-appointments-container');
        if (!appointmentsContainer) return;

        appointmentsContainer.innerHTML = '<p>Loading appointments...</p>';

        fetch('../php/getAdminAppointments.php')
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    appointmentsContainer.innerHTML = '<p>Unable to load appointments.</p>';
                    console.error('Unable to load admin appointments:', data?.error);
                    return;
                }

                renderAdminAppointments(data.appointments || []);
            })
            .catch(error => {
                console.error('Error fetching admin appointments:', error);
                appointmentsContainer.innerHTML = '<p>Unable to load appointments.</p>';
            });
    }

    function renderAdminAppointments(appointments) {
        const appointmentsContainer = document.getElementById('admin-appointments-container');
        if (!appointmentsContainer) return;

        if (!appointments.length) {
            appointmentsContainer.innerHTML = '<p>No appointments found.</p>';
            return;
        }

        const appointmentsByStatus = {
            REQUESTED: [],
            CONFIRMED: [],
            COMPLETED: [],
            CANCELLED: []
        };

        appointments.forEach(apt => {
            const status = apt.appointment_status || 'REQUESTED';
            if (appointmentsByStatus[status]) {
                appointmentsByStatus[status].push(apt);
            }
        });

        let html = '<div class="admin-appointments-view">';

        Object.entries(appointmentsByStatus).forEach(([status, appts]) => {
            if (appts.length > 0) {
                html += `
                    <div class="appointments-section">
                        <h3>${status.replace('_', ' ')} (${appts.length})</h3>
                        <div class="appointments-grid">
                            ${appts.map(apt => {
                                const patientName = `${apt.patient_first_name || ''} ${apt.patient_last_name || ''}`.trim() || `Patient #${apt.patient_id}`;
                                const doctorName = `${apt.doctor_first_name || ''} ${apt.doctor_last_name || ''}`.trim() || `Doctor #${apt.doctor_id}`;
                                return `
                                    <div class="appointment-card">
                                        <div class="appointment-card-header">
                                            <p><strong>${escapeHtml(patientName)}</strong> → <strong>${escapeHtml(doctorName)}</strong></p>
                                            <span class="appointment-status">${escapeHtml(status)}</span>
                                        </div>
                                        <p><strong>Date:</strong> ${escapeHtml(apt.appointment_date)}</p>
                                        <p><strong>Time:</strong> ${escapeHtml(apt.appointment_time)}</p>
                                        <p><strong>Reason:</strong> ${escapeHtml(apt.appointment_reason || 'N/A')}</p>
                                        <p><strong>Created:</strong> ${escapeHtml(apt.created_at || '—')}</p>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }
        });

        html += '</div>';
        appointmentsContainer.innerHTML = html;
    }

    function renderAdminSettings(settings) {
        if (!settingsList) return;

        const entries = Object.entries(settings);
        if (!entries.length) {
            settingsList.innerHTML = '<li>No settings available.</li>';
            return;
        }

        settingsList.innerHTML = entries.map(([key, value]) => `
            <li><strong>${key.replace(/_/g, ' ')}:</strong> ${value}</li>
        `).join('');
    }

    function setupThemeControls() {
        if (cardRadiusInput && cardRadiusValue) {
            cardRadiusValue.textContent = `${cardRadiusInput.value}px`;
            cardRadiusInput.addEventListener('input', () => {
                cardRadiusValue.textContent = `${cardRadiusInput.value}px`;
            });
        }

        if (applyThemeBtn) {
            applyThemeBtn.addEventListener('click', () => {
                const theme = themeSelect?.value || 'light';
                const accentColor = accentColorInput?.value || '#4F46E5';
                const cardRadius = parseInt(cardRadiusInput?.value || '16', 10);
                const settings = { theme, accentColor, cardRadius };
                applyTheme(settings);
                saveThemeSettings(settings);
            });
        }

        if (resetThemeBtn) {
            resetThemeBtn.addEventListener('click', () => {
                const defaults = { theme: 'light', accentColor: '#4F46E5', cardRadius: 16 };
                if (themeSelect) themeSelect.value = defaults.theme;
                if (accentColorInput) accentColorInput.value = defaults.accentColor;
                if (cardRadiusInput) cardRadiusInput.value = defaults.cardRadius;
                if (cardRadiusValue) cardRadiusValue.textContent = `${defaults.cardRadius}px`;
                applyTheme(defaults);
                saveThemeSettings(defaults);
            });
        }
    }

    function loadStoredTheme() {
        const stored = getStoredThemeSettings();
        if (!stored) {
            applyTheme({ theme: 'light', accentColor: '#4F46E5', cardRadius: 16 });
            return;
        }

        if (themeSelect) themeSelect.value = stored.theme;
        if (accentColorInput) accentColorInput.value = stored.accentColor;
        if (cardRadiusInput) {
            cardRadiusInput.value = stored.cardRadius;
            if (cardRadiusValue) cardRadiusValue.textContent = `${stored.cardRadius}px`;
        }
        applyTheme(stored);
    }

    function saveThemeSettings(settings) {
        localStorage.setItem('adminDashboardTheme', JSON.stringify(settings));
    }

    function getStoredThemeSettings() {
        const data = localStorage.getItem('adminDashboardTheme');
        if (!data) return null;
        try {
            return JSON.parse(data);
        } catch (e) {
            return null;
        }
    }

    function applyTheme(settings) {
        document.body.classList.remove('theme-light', 'theme-dark', 'theme-blue');
        document.body.classList.add(`theme-${settings.theme}`);
        document.documentElement.style.setProperty('--accent-color', settings.accentColor);
        document.documentElement.style.setProperty('--card-radius', `${settings.cardRadius}px`);
        if (currentThemeLabel) {
            currentThemeLabel.textContent = `${settings.theme.charAt(0).toUpperCase()}${settings.theme.slice(1)}`;
        }
    }

    function getThemeDefaults() {
        return { theme: 'light', accentColor: '#4F46E5', cardRadius: 16 };
    }

    function restoreThemeControls() {
        const defaults = getThemeDefaults();
        if (themeSelect) themeSelect.value = defaults.theme;
        if (accentColorInput) accentColorInput.value = defaults.accentColor;
        if (cardRadiusInput) cardRadiusInput.value = defaults.cardRadius;
        if (cardRadiusValue) cardRadiusValue.textContent = `${defaults.cardRadius}px`;
    }

    function renderDoctorAppointments(appointments) {
        if (!doctorAppointments) return;

        const pendingAppointments = appointments.filter(a => a.appointment_status === 'REQUESTED');
        const confirmedAppointments = appointments.filter(a => a.appointment_status === 'CONFIRMED');

        if (!pendingAppointments.length) {
            if (doctorAppointments) doctorAppointments.innerHTML = '<p class="no-appointments">No appointment requests at this time.</p>';
        } else if (doctorAppointments) {
            doctorAppointments.innerHTML = pendingAppointments.map(appointment => {
                const patientName = appointment.patient_first_name && appointment.patient_last_name
                    ? `${appointment.patient_first_name} ${appointment.patient_last_name}`
                    : `Patient #${appointment.patient_id}`;
                const createdAt = appointment.created_at ? new Date(appointment.created_at).toLocaleString() : '';
                return `
                    <div class="appointment-card">
                        <div class="appointment-card-header">
                            <p><strong>Patient:</strong> ${escapeHtml(patientName)}</p>
                            <span class="appointment-status">Requested</span>
                        </div>
                        <p><strong>Date:</strong> ${escapeHtml(appointment.appointment_date)}</p>
                        <p><strong>Time:</strong> ${escapeHtml(appointment.appointment_time)}</p>
                        <p><strong>Reason:</strong> ${escapeHtml(appointment.appointment_reason || 'N/A')}</p>
                        ${createdAt ? `<p><strong>Requested on:</strong> ${escapeHtml(createdAt)}</p>` : ''}
                        <div class="appointment-actions">
                            <button class="book-btn accept-btn" data-id="${escapeHtml(appointment.appointment_id)}" data-status="CONFIRMED">Accept</button>
                            <button class="book-btn reject-btn" data-id="${escapeHtml(appointment.appointment_id)}" data-status="CANCELLED">Reject</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        if (!confirmedAppointments.length) {
            if (approvedAppointments) approvedAppointments.innerHTML = '<p class="no-appointments">No approved appointments yet.</p>';
        } else if (approvedAppointments) {
            approvedAppointments.innerHTML = confirmedAppointments.map(appointment => {
                const patientName = appointment.patient_first_name && appointment.patient_last_name
                    ? `${appointment.patient_first_name} ${appointment.patient_last_name}`
                    : `Patient #${appointment.patient_id}`;
                const updatedAt = appointment.updated_at ? new Date(appointment.updated_at).toLocaleString() : '';
                return `
                    <div class="appointment-card">
                        <div class="appointment-card-header">
                            <p><strong>Patient:</strong> ${escapeHtml(patientName)}</p>
                            <span class="appointment-status">Confirmed</span>
                        </div>
                        <p><strong>Date:</strong> ${escapeHtml(appointment.appointment_date)}</p>
                        <p><strong>Time:</strong> ${escapeHtml(appointment.appointment_time)}</p>
                        <p><strong>Reason:</strong> ${escapeHtml(appointment.appointment_reason || 'N/A')}</p>
                        ${updatedAt ? `<p><strong>Confirmed on:</strong> ${escapeHtml(updatedAt)}</p>` : ''}
                    </div>
                `;
            }).join('');
        }

        updateApprovedAppointmentCount(confirmedAppointments.length);
        updateDashboardSummary(appointments);
        renderMedicalRecordForm(appointments);
        renderPatientList(appointments);

        if (doctorAppointments) {
            doctorAppointments.querySelectorAll('.accept-btn, .reject-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    updateAppointmentStatus(btn.dataset.id, btn.dataset.status);
                });
            });
        }
    }

    function renderMedicalRecordForm(appointments) {
        if (!medicalRecordAppointmentSelect || !medicalRecordForm) return;

        const selectableAppointments = appointments.filter(a => a.appointment_status === 'CONFIRMED');
        medicalRecordAppointmentSelect.innerHTML = '<option value="">Select a confirmed appointment</option>';

        if (!selectableAppointments.length) {
            medicalRecordAppointmentSelect.innerHTML += '<option value="" disabled>No confirmed appointments available</option>';
            medicalRecordForm.querySelector('button[type="submit"]').disabled = true;
            return;
        }

        selectableAppointments.forEach(appointment => {
            const patientName = appointment.patient_first_name && appointment.patient_last_name
                ? `${appointment.patient_first_name} ${appointment.patient_last_name}`
                : `Patient #${appointment.patient_id}`;
            const optionLabel = `${patientName} — ${appointment.appointment_date} ${appointment.appointment_time}`;
            const option = document.createElement('option');
            option.value = appointment.appointment_id;
            option.textContent = optionLabel;
            option.dataset.patientId = appointment.patient_id;
            option.dataset.doctorId = appointment.doctor_id;
            medicalRecordAppointmentSelect.appendChild(option);
        });

        medicalRecordForm.querySelector('button[type="submit"]').disabled = false;
    }

    function handleMedicalRecordSubmit(event) {
        event.preventDefault();
        if (!medicalRecordAppointmentSelect) return;

        const appointmentId = medicalRecordAppointmentSelect.value;
        const selectedOption = medicalRecordAppointmentSelect.selectedOptions[0];
        const patientId = selectedOption ? selectedOption.dataset.patientId : null;
        const diagnosis = medicalRecordDiagnosis ? medicalRecordDiagnosis.value.trim() : '';
        const prescription = medicalRecordPrescription ? medicalRecordPrescription.value.trim() : '';
        const treatmentNotes = medicalRecordTreatment ? medicalRecordTreatment.value.trim() : '';

        if (!appointmentId || !patientId) {
            showMedicalRecordFeedback('Please select a confirmed appointment to complete.', true);
            return;
        }

        if (!diagnosis && !prescription && !treatmentNotes) {
            showMedicalRecordFeedback('Please add at least one medical record field before saving.', true);
            return;
        }

        fetch('../php/saveMedicalRecord.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                patient_id: patientId,
                diagnosis,
                prescription,
                treatment_notes: treatmentNotes
            })
        })
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    showMedicalRecordFeedback(data.error || 'Unable to save the medical record.', true);
                    return;
                }

                showMedicalRecordFeedback(data.message || 'Medical record saved and appointment completed.');
                if (medicalRecordForm) {
                    medicalRecordForm.reset();
                }
                fetchDoctorAppointments();
            })
            .catch(error => {
                console.error('Error saving medical record:', error);
                showMedicalRecordFeedback('A network error occurred while saving the medical record.', true);
            });
    }

    function showMedicalRecordFeedback(message, isError = false) {
        if (!medicalRecordFeedback) return;
        medicalRecordFeedback.textContent = message;
        medicalRecordFeedback.style.color = isError ? '#c00' : '#0a7';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function handleAppointmentStatusSubmit(event) {
        event.preventDefault();
        if (!statusAppointmentSelect || !appointmentStatusSelect) return;

        const appointmentId = statusAppointmentSelect.value;
        const status = appointmentStatusSelect.value;

        if (!appointmentId || !status) {
            showStatusUpdateFeedback('Please select an appointment and a status.', true);
            return;
        }

        fetch('../php/updateAppointmentStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ appointment_id: appointmentId, status })
        })
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    showStatusUpdateFeedback(data.error || 'Unable to update appointment status.', true);
                    return;
                }

                showStatusUpdateFeedback(data.message || 'Appointment status updated successfully.');
                fetchDoctorAppointments();
            })
            .catch(error => {
                console.error('Error updating appointment status:', error);
                showStatusUpdateFeedback('A network error occurred while updating the appointment.', true);
            });
    }

    function showStatusUpdateFeedback(message, isError = false) {
        if (!statusUpdateFeedback) return;
        statusUpdateFeedback.textContent = message;
        statusUpdateFeedback.style.color = isError ? '#c00' : '#0a7';
    }

    function handleAdminUserCreateSubmit(event) {
        event.preventDefault();
        if (!adminUserCreateForm) return;

        const firstName = document.getElementById('admin-first-name')?.value.trim();
        const lastName = document.getElementById('admin-last-name')?.value.trim();
        const email = document.getElementById('admin-email')?.value.trim();
        const password = document.getElementById('admin-password')?.value;
        const gender = document.getElementById('admin-gender')?.value;
        const role = document.getElementById('admin-role')?.value;
        const accountStatus = document.getElementById('admin-account-status')?.value;

        if (!firstName || !lastName || !email || !password || !gender || !role || !accountStatus) {
            showAdminCreateUserFeedback('All fields are required.', true);
            return;
        }

        fetch('../php/adminCreateUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ first_name: firstName, last_name: lastName, email, password, gender, role, account_status: accountStatus })
        })
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    showAdminCreateUserFeedback(data.error || 'Unable to create user account.', true);
                    return;
                }

                showAdminCreateUserFeedback(data.message || 'User account created successfully.');
                adminUserCreateForm.reset();
                const userFormContainer = document.getElementById('admin-user-form-container');
                if (userFormContainer) {
                    userFormContainer.style.display = 'none';
                }
                fetchAdminUsers();
            })
            .catch(error => {
                console.error('Error creating admin user:', error);
                showAdminCreateUserFeedback('A network error occurred while creating the user.', true);
            });
    }

    function showAdminCreateUserFeedback(message, isError = false) {
        if (!adminCreateUserFeedback) return;
        adminCreateUserFeedback.textContent = message;
        adminCreateUserFeedback.style.color = isError ? '#c00' : '#0a7';
    }

    function handleDoctorSearch() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const filter = filterOptions ? filterOptions.value : '';
        const filteredAppointments = filterAppointments(doctorAppointmentCache, query, filter);
        renderDoctorAppointments(filteredAppointments);
        renderPatientList(filteredAppointments);
    }

    function filterAppointments(appointments, query, filter) {
        if (!query) {
            return appointments;
        }

        return appointments.filter(appointment => {
            const patientName = `${appointment.patient_first_name || ''} ${appointment.patient_last_name || ''}`.trim().toLowerCase();
            const patientId = appointment.patient_id ? appointment.patient_id.toString() : '';
            const doctorName = `${appointment.doctor_first_name || ''} ${appointment.doctor_last_name || ''}`.trim().toLowerCase();
            const appointmentDate = appointment.appointment_date ? appointment.appointment_date.toLowerCase() : '';
            const doctorSpecialty = appointment.doctor_specialty ? appointment.doctor_specialty.toLowerCase() : '';

            switch (filter) {
                case 'name':
                    return patientName.includes(query);
                case 'id':
                    return patientId.includes(query);
                case 'doctor':
                    return doctorName.includes(query) || doctorSpecialty.includes(query);
                case 'date':
                    return appointmentDate.includes(query);
                default:
                    return (
                        patientName.includes(query) ||
                        patientId.includes(query) ||
                        doctorName.includes(query) ||
                        doctorSpecialty.includes(query) ||
                        appointmentDate.includes(query)
                    );
            }
        });
    }

    function renderPatientList(appointments) {
        if (!patientList) return;

        if (!appointments.length) {
            patientList.innerHTML = '<p class="no-appointments">No matching patients found.</p>';
            return;
        }

        const uniquePatients = {};
        appointments.forEach(appointment => {
            const key = appointment.patient_id;
            if (!uniquePatients[key]) {
                uniquePatients[key] = {
                    patientId: appointment.patient_id,
                    name: `${appointment.patient_first_name || ''} ${appointment.patient_last_name || ''}`.trim() || `Patient #${appointment.patient_id}`,
                    appointments: []
                };
            }
            uniquePatients[key].appointments.push(appointment);
        });

        patientList.innerHTML = Object.values(uniquePatients).map(patient => {
            const appointmentCount = patient.appointments.length;
            const nextAppt = patient.appointments.sort((a, b) => (a.appointment_date + a.appointment_time).localeCompare(b.appointment_date + b.appointment_time))[0];
            return `
                <div class="appointment-card">
                    <div class="appointment-card-header">
                        <p><strong>${escapeHtml(patient.name)}</strong></p>
                        <span class="appointment-status">${appointmentCount} appt${appointmentCount > 1 ? 's' : ''}</span>
                    </div>
                    <p><strong>Next visit:</strong> ${escapeHtml(nextAppt.appointment_date)} at ${escapeHtml(nextAppt.appointment_time)}</p>
                    <p><strong>Status:</strong> ${escapeHtml(nextAppt.appointment_status.replace('_', ' '))}</p>
                </div>
            `;
        }).join('');
    }

    function populateAppointmentStatusOptions(appointments) {
        if (!statusAppointmentSelect) return;
        statusAppointmentSelect.innerHTML = '<option value="">Choose appointment</option>';

        const confirmedOrPending = appointments.filter(a => ['REQUESTED', 'CONFIRMED'].includes(a.appointment_status));
        confirmedOrPending.forEach(appointment => {
            const label = `${appointment.patient_first_name || ''} ${appointment.patient_last_name || ''}`.trim() || `Patient #${appointment.patient_id}`;
            const optionText = `${escapeHtml(label)} — ${escapeHtml(appointment.appointment_date)} ${escapeHtml(appointment.appointment_time)} (${escapeHtml(appointment.appointment_status)})`;
            const option = document.createElement('option');
            option.value = appointment.appointment_id;
            option.textContent = optionText;
            statusAppointmentSelect.appendChild(option);
        });
    }

    function updateApprovedAppointmentCount(count) {
        if (!appointmentCountLabel) return;
        appointmentCountLabel.textContent = count.toString();
    }

    function updateDashboardSummary(appointments) {
        if (summaryPending) {
            const pendingCount = appointments.filter(a => a.appointment_status === 'REQUESTED').length;
            summaryPending.textContent = pendingCount.toString();
        }
        if (summaryConfirmed) {
            const confirmedCount = appointments.filter(a => a.appointment_status === 'CONFIRMED').length;
            summaryConfirmed.textContent = confirmedCount.toString();
        }
        if (summaryCompleted) {
            const completedCount = appointments.filter(a => a.appointment_status === 'COMPLETED').length;
            summaryCompleted.textContent = completedCount.toString();
        }
    }

    function setupRouteNavigation() {
        routeLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const route = link.dataset.route;
                renderRoute(route);
            });
        });

        const initialRoute = window.location.hash ? window.location.hash.replace('#', '') : 'dashboard';
        renderRoute(initialRoute);
    }

    function renderRoute(route) {
        Object.keys(routePages).forEach(key => {
            const page = routePages[key];
            if (!page) return;
            page.classList.toggle('hidden', key !== route);
        });

        routeLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.route === route);
        });

        if (isDoctorPage && route === 'appointments') {
            fetchDoctorAppointments();
        }

        if (isDoctorPage && route === 'messages') {
            fetchMessages();
        }

        if (isDoctorPage && route === 'medical-records') {
            renderMedicalRecordForm(doctorAppointmentCache);
        }

        if (isAdminPage && route === 'dashboard') {
            fetchAdminOverview();
        }

        if (isAdminPage && route === 'appointments') {
            fetchAdminAppointments();
        }

        if (isAdminPage && route === 'user-access') {
            fetchAdminUsers();
        }

        if (isAdminPage && route === 'messages') {
            fetchLogs();
        }

        if (isAdminPage && route === 'settings') {
            fetchAdminSettings();
        }

        if (isPatientPage && route === 'appointments') {
            const section = document.getElementById('appointments-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function updateAppointmentStatus(appointmentId, status) {
        if (!doctorAppointments) return;

        fetch('../php/updateAppointmentStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ appointment_id: appointmentId, status })
        })
            .then(response => response.json())
            .then(data => {
                if (!data || data.error) {
                    alert(data.error || 'Unable to update appointment status.');
                    return;
                }
                fetchDoctorAppointments();
            })
            .catch(error => {
                console.error('Error updating appointment status:', error);
                alert('There was a problem updating the appointment.');
            });
    }

    function showFeedback(message, isError = false) {
        if (!bookingFeedback) return;
        bookingFeedback.textContent = message;
        bookingFeedback.style.color = isError ? '#c00' : '#0a7';
    }

    function clearFeedback() {
        if (!bookingFeedback) return;
        bookingFeedback.textContent = '';
    }

    function renderScheduledAppointments(appointments) {
        if (!scheduledAppointments) return;

        if (appointmentCountLabel) {
            appointmentCountLabel.textContent = appointments.length.toString();
        }

        if (!appointments.length) {
            scheduledAppointments.innerHTML = '<p>No booked appointments.</p>';
            return;
        }

        scheduledAppointments.innerHTML = appointments.map(appointment => {
            const doctorName = appointment.doctor_name || `Doctor #${appointment.doctor_id}`;
            const doctorSpecialty = appointment.doctor_specialty ? ` (${appointment.doctor_specialty})` : '';
            const statusLabel = appointment.appointment_status ? appointment.appointment_status.replace('_', ' ') : 'Unknown';
            const createdAt = appointment.created_at ? new Date(appointment.created_at).toLocaleString() : '';
            return `
                <div class="appointment-card">
                    <div class="appointment-card-header">
                        <p><strong>Doctor:</strong> ${escapeHtml(doctorName)}${doctorSpecialty ? ` (${escapeHtml(appointment.doctor_specialty)})` : ''}</p>
                        <span class="appointment-status">${escapeHtml(statusLabel)}</span>
                    </div>
                    <p><strong>Date:</strong> ${escapeHtml(appointment.appointment_date)}</p>
                    <p><strong>Time:</strong> ${escapeHtml(appointment.appointment_time)}</p>
                    ${createdAt ? `<p><strong>Booked on:</strong> ${escapeHtml(createdAt)}</p>` : ''}
                </div>
            `;
        }).join('');
    }

    function appendAppointmentToDOM(appointment) {
        if (!scheduledAppointments) return;

        if (appointmentCountLabel) {
            const currentCount = scheduledAppointments.querySelectorAll('.appointment-card').length + 1;
            appointmentCountLabel.textContent = currentCount.toString();
        }

        // Ensure array container exists
        if (!scheduledAppointments.querySelector('.appointment-card')) {
            // If previously showed placeholder text, clear it
            if (scheduledAppointments.textContent && scheduledAppointments.textContent.includes('No booked')) {
                scheduledAppointments.innerHTML = '';
            }
        }

        const doctorName = appointment.doctor_name || (selectedDoctor ? selectedDoctor.name : `Doctor #${appointment.doctor_id}`);
        const doctorSpecialty = appointment.doctor_specialty || (selectedDoctor ? selectedDoctor.specialty : '');
        const statusLabel = appointment.appointment_status ? appointment.appointment_status.replace('_', ' ') : 'Unknown';
        const createdAt = appointment.created_at ? new Date(appointment.created_at).toLocaleString() : new Date().toLocaleString();

        const cardHtml = `
            <div class="appointment-card">
                <div class="appointment-card-header">
                    <p><strong>Doctor:</strong> ${escapeHtml(doctorName)}${doctorSpecialty ? ' (' + escapeHtml(doctorSpecialty) + ')' : ''}</p>
                    <span class="appointment-status">${escapeHtml(statusLabel)}</span>
                </div>
                <p><strong>Date:</strong> ${escapeHtml(appointment.appointment_date)}</p>
                <p><strong>Time:</strong> ${escapeHtml(appointment.appointment_time)}</p>
                ${createdAt ? `<p><strong>Booked on:</strong> ${escapeHtml(createdAt)}</p>` : ''}
            </div>
        `;

        // Prepend newest appointment
        scheduledAppointments.innerHTML = cardHtml + scheduledAppointments.innerHTML;
    }

    function fetchUserData() {
        fetch('../php/api.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.log(data.error);
                    return;
                }

                userName.textContent = `${data.user_name} ${data.user_surname}`;
                userId.textContent = `ID: ${data.user_id}`;
                userInitials.textContent = data.user_initials;
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
            });
    }


   function fetchMessages() {
    fetch('../php/getNotifications.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('route-messages');
            if (!container) return;

            if (!data.success) {
                container.innerHTML = '<p>No messages to display.</p>';
                return;
            }

            const messages = data.messages || [];
            if (messages.length === 0) {
                container.innerHTML = '<p>No messages to display.</p>';
                return;
            }

            const html = messages.map(msg => `
                <div class="message-card">
                    <h4>${escapeHtml(msg.notification_title)}</h4>
                    <p>${escapeHtml(msg.notification_message)}</p>
                    <small><b>Received: </b> ${escapeHtml(msg.created_at || 'N/A')}</small>
                </div>
            `).join('');

            container.innerHTML = `
                <h2>Messages</h2>
                ${html}
            `;
        })
        .catch(err => {
            console.error('Error fetching messages:', err);
            document.getElementById('route-messages').innerHTML = '<p>Unable to load messages.</p>';
        });
}


function fetchLogs() {
    fetch('../php/getLogs.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('route-messages');
            if (!container) return;

            if (!data.success) {
                container.innerHTML = '<p>No messages to display.</p>';
                return;
            }

            const logs = data.logs || [];
            if (logs.length === 0) {
                container.innerHTML = '<p>No messages to display.</p>';
                return;
            }

            const html = logs.map(msg => `
                <div class="message-card">
                    <h4>${escapeHtml(msg.user_name)}</h4>
                    <strong>${escapeHtml(msg.action_performed)}</strong>
                    <p>${escapeHtml(msg.action_description)}</p>
                    <small><b>Received:</b> ${escapeHtml(msg.created_at || 'N/A')}</small>
                </div>
            `).join('');

            container.innerHTML = `
                <h2>Audit Logs</h2>
                ${html}
            `;
        })
        .catch(err => {
            console.error('Error fetching messages:', err);
            document.getElementById('route-messages').innerHTML = '<p>Unable to load messages.</p>';
        });
}

});




