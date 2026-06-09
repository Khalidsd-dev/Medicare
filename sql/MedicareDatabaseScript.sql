CREATE DATABASE IF NOT EXISTS medicare;
USE medicare;


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



CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,

    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,

    appointment_status ENUM(
        'REQUESTED',
        'CONFIRMED',
        'COMPLETED',
        'CANCELLED'
    ) DEFAULT 'REQUESTED',

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
    ('Alice', 'Patient', 'patient@example.com', '$2y$10$A5spv0x5wNMzMAIR5QJE/uFe07mXceAe3fRe228125DuCoYCYC5FS', 'FEMALE', 'PATIENT', 'ACTIVE'),
    ('Brian', 'Doctor', 'doctor@example.com', ' $2y$10$A5spv0x5wNMzMAIR5QJE/uFe07mXceAe3fRe228125DuCoYCYC5FS', 'MALE', 'DOCTOR', 'ACTIVE'),
    ('Claire', 'Admin', 'admin@example.com', '$2y$10$A5spv0x5wNMzMAIR5QJE/uFe07mXceAe3fRe228125DuCoYCYC5FS', 'OTHER', 'ADMIN', 'ACTIVE'),
    ('David', 'Patient', 'patient2@example.com', '$2y$10$A5spv0x5wNMzMAIR5QJE/uFe07mXceAe3fRe228125DuCoYCYC5FS', 'MALE', 'PATIENT', 'ACTIVE'),
    ('Emily', 'Doctor', 'doctor2@example.com', '$2y$10$A5spv0x5wNMzMAIR5QJE/uFe07mXceAe3fRe228125DuCoYCYC5FS', 'FEMALE', 'DOCTOR', 'ACTIVE');

INSERT INTO patients (patient_id, date_of_birth, phone_number, address, blood_group, emergency_contact)
VALUES  (1, '1990-01-01', '0781456872', '123 Main St, Anytown, USA', 'O+', 'John Doe - 555-5678'),
        (4, '1985-05-15', '0675421364', '456 Elm St, Othertown, USA', 'A-', 'Jane Doe - 555-4321');

INSERT INTO doctors (doctor_id, specialization, doctor_phone, consultation_fee, doctor_status)
VALUES  (2, 'Cardiologist', '555-5678', 100.00, 'AVAILABLE'),
        (5, 'Dermatologist', '555-4321', 80.00, 'AVAILABLE');

INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time,  appointment_status, created_at, updated_at)
VALUES
    (1, 2, '2026-06-10', '10:00:00', 'CONFIRMED', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (4, 5, '2026-06-11', '14:00:00', 'REQUESTED', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    (1, 5, '2026-06-12', '09:00:00', 'CANCELLED', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, prescription, treatment_notes)
VALUES
    (1, 2, 1, 'Healthy with mild fatigue', 'Vitamin D supplements', 'Advise follow-up in four weeks'),
    (4, 5, 2, 'Mild eczema', 'Topical corticosteroids', 'Recommend moisturizing and follow-up in two weeks'),
    (1, 5, 3, 'Cancelled appointment', 'No prescription', 'Appointment was cancelled by the patient.');

INSERT INTO notifications (user_id, notification_title, notification_message, notification_status)
VALUES
    (1, 'Appointment Confirmed', 'Your appointment with Dr. Brian Doctor has been confirmed for 2026-06-10 10:00.', 'UNREAD'),
    (2, 'New Appointment', 'A new appointment request has been created for Alice Patient.', 'UNREAD'),
    (4, 'Appointment Requested', 'Your appointment request with Dr. Emily Doctor is pending confirmation.', 'UNREAD'),
    (5, 'Appointment Cancelled', 'Your appointment with Dr. Emily Doctor has been cancelled.', 'UNREAD');

INSERT INTO audit_logs (user_id, action_performed, action_description)
VALUES
    (3, 'Seed data loaded', 'Initial sample users, appointments, and records inserted.'),
    (1, 'Logged in', 'Patient Alice Patient logged in.'),
    (2, 'Logged in', 'Doctor Brian Doctor logged in.'),
    (4, 'Logged in', 'Patient David Patient logged in.'),
    (5, 'Logged in', 'Doctor Emily Doctor logged in.');
