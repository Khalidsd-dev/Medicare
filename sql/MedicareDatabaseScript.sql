CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,

    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,

    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,

    gender ENUM('MALE', 'FEMALE', 'OTHER') NOT NULL,

    user_role ENUM(
        'PATIENT',
        'DOCTOR',
        'ADMIN'
    ) NOT NULL,

    account_status ENUM(
        'ACTIVE',
        'INACTIVE',
        'SUSPENDED'
    ) DEFAULT 'ACTIVE',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
    patient_id INT PRIMARY KEY,

    date_of_birth DATE,
    phone_number VARCHAR(20),
    address TEXT,
    blood_group VARCHAR(10),
    emergency_contact VARCHAR(100),

    CONSTRAINT fk_patient_user
    FOREIGN KEY (patient_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

CREATE TABLE doctors (
    doctor_id INT PRIMARY KEY,

    specialization VARCHAR(150) NOT NULL,
    doctor_phone VARCHAR(20),
    consultation_fee DECIMAL(10,2),

    doctor_status ENUM(
        'AVAILABLE',
        'UNAVAILABLE'
    ) DEFAULT 'AVAILABLE',

    CONSTRAINT fk_doctor_user
    FOREIGN KEY (doctor_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);


CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) UNIQUE NOT NULL,
    department_description TEXT
);

CREATE TABLE doctor_departments (
    doctor_department_id INT AUTO_INCREMENT PRIMARY KEY,

    doctor_id INT NOT NULL,
    department_id INT NOT NULL,

    CONSTRAINT fk_dd_doctor
    FOREIGN KEY (doctor_id)
    REFERENCES doctors(doctor_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_dd_department
    FOREIGN KEY (department_id)
    REFERENCES departments(department_id)
    ON DELETE CASCADE
);

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,

    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,

    appointment_reason TEXT,

    appointment_status ENUM(
        'REQUESTED',
        'CONFIRMED',
        'COMPLETED',
        'CANCELLED'
    ) DEFAULT 'REQUESTED',

    cancellation_reason TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_appointment_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_appointment_doctor
    FOREIGN KEY (doctor_id)
    REFERENCES doctors(doctor_id)
    ON DELETE CASCADE
);

CREATE TABLE appointment_status_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,

    appointment_id INT NOT NULL,

    old_status ENUM(
        'REQUESTED',
        'CONFIRMED',
        'COMPLETED',
        'CANCELLED'
    ),

    new_status ENUM(
        'REQUESTED',
        'CONFIRMED',
        'COMPLETED',
        'CANCELLED'
    ) NOT NULL,

    changed_by INT,

    notes TEXT,

    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_status_history_appointment
    FOREIGN KEY (appointment_id)
    REFERENCES appointments(appointment_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_status_history_user
    FOREIGN KEY (changed_by)
    REFERENCES users(user_id)
    ON DELETE SET NULL
);


CREATE TABLE medical_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_id INT,

    diagnosis TEXT,
    prescription TEXT,
    treatment_notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_medical_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_medical_doctor
    FOREIGN KEY (doctor_id)
    REFERENCES doctors(doctor_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_medical_appointment
    FOREIGN KEY (appointment_id)
    REFERENCES appointments(appointment_id)
    ON DELETE SET NULL
);

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    notification_title VARCHAR(255),
    notification_message TEXT,

    notification_status ENUM(
        'READ',
        'UNREAD'
    ) DEFAULT 'UNREAD',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notification_user
    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT,
    action_performed VARCHAR(255),
    action_description TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE SET NULL
);

-- Sample data for initial testing
-- Note: seeded patient, doctor, and admin accounts all use password "password".
INSERT INTO users (first_name, last_name, email, password, gender, user_role, account_status)
VALUES
    ('Alice', 'Patient', 'patient@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.T8eQYQxT4HqQX0kW', 'FEMALE', 'PATIENT', 'ACTIVE'),
    ('Brian', 'Doctor', 'doctor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.T8eQYQxT4HqQX0kW', 'MALE', 'DOCTOR', 'ACTIVE'),
    ('Claire', 'Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.T8eQYQxT4HqQX0kW', 'OTHER', 'ADMIN', 'ACTIVE');

INSERT INTO departments (department_name, department_description)
VALUES
    ('General Medicine', 'Primary care and general consultations'),
    ('Cardiology', 'Heart and cardiovascular care');

INSERT INTO doctor_departments (doctor_id, department_id)
VALUES
    (2, 1),
    (2, 2);

INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, appointment_reason, appointment_status, created_at, updated_at)
VALUES
    (1, 2, '2026-06-10', '10:00:00', 'Routine checkup', 'CONFIRMED', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, notes)
VALUES
    (1, 'REQUESTED', 'CONFIRMED', 2, 'Confirmed by doctor.');

INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, prescription, treatment_notes)
VALUES
    (1, 2, 1, 'Healthy with mild fatigue', 'Vitamin D supplements', 'Advise follow-up in four weeks');

INSERT INTO notifications (user_id, notification_title, notification_message, notification_status)
VALUES
    (1, 'Appointment Confirmed', 'Your appointment with Dr. Brian Doctor has been confirmed for 2026-06-10 10:00.', 'UNREAD'),
    (2, 'New Appointment', 'A new appointment request has been created for Alice Patient.', 'UNREAD');

INSERT INTO audit_logs (user_id, action_performed, action_description)
VALUES
    (3, 'Seed data loaded', 'Initial sample users, appointments, and records inserted.');

DELIMITER //

CREATE TRIGGER trg_create_patient
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.user_role = 'PATIENT' THEN
        INSERT INTO patients(patient_id)
        VALUES (NEW.user_id);
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_create_doctor
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.user_role = 'DOCTOR' THEN
        INSERT INTO doctors(doctor_id)
        VALUES (NEW.user_id);
    END IF;
END //

DELIMITER ;

CREATE VIEW appointment_view AS
SELECT
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.appointment_status,

    p.patient_id,
    pu.first_name AS patient_first_name,
    pu.last_name AS patient_last_name,

    d.doctor_id,
    du.first_name AS doctor_first_name,
    du.last_name AS doctor_last_name,

    a.created_at

FROM appointments a

INNER JOIN patients p
    ON a.patient_id = p.patient_id

INNER JOIN users pu
    ON p.patient_id = pu.user_id

INNER JOIN doctors d
    ON a.doctor_id = d.doctor_id

INNER JOIN users du
    ON d.doctor_id = du.user_id;