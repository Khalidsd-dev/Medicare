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