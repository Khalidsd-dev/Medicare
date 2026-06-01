# Medicare System ERD

## Entities

- `users`
  - Primary user store for all roles: PATIENT, DOCTOR, ADMIN
  - Attributes: user_id, first_name, last_name, email, password, gender, user_role, account_status, created_at

- `patients`
  - One-to-one extension for PATIENT users
  - Attributes: patient_id (PK, FK to users.user_id), date_of_birth, phone_number, address, blood_group, emergency_contact

- `doctors`
  - One-to-one extension for DOCTOR users
  - Attributes: doctor_id (PK, FK to users.user_id), specialization, doctor_phone, consultation_fee, doctor_status

- `departments`
  - Lists medical departments
  - Attributes: department_id, department_name, department_description

- `doctor_departments`
  - Many-to-many between doctors and departments
  - Attributes: doctor_department_id, doctor_id, department_id

- `appointments`
  - Links patients with doctors
  - Attributes: appointment_id, patient_id, doctor_id, appointment_date, appointment_time, appointment_reason, appointment_status, cancellation_reason, created_at, updated_at

- `appointment_status_history`
  - Tracks appointment status changes over time
  - Attributes: history_id, appointment_id, old_status, new_status, changed_by, notes, changed_at

- `medical_records`
  - Stores diagnoses and treatment notes for completed appointments
  - Attributes: record_id, patient_id, doctor_id, appointment_id, diagnosis, prescription, treatment_notes, created_at

- `notifications`
  - User notifications
  - Attributes: notification_id, user_id, notification_title, notification_message, notification_status, created_at

- `audit_logs`
  - System audit history
  - Attributes: log_id, user_id, action_performed, action_description, created_at

## Relationships

- `users.user_id` -> `patients.patient_id` (1:1)
- `users.user_id` -> `doctors.doctor_id` (1:1)
- `doctors.doctor_id` -> `doctor_departments.doctor_id` (1:N)
- `departments.department_id` -> `doctor_departments.department_id` (1:N)
- `patients.patient_id` -> `appointments.patient_id` (1:N)
- `doctors.doctor_id` -> `appointments.doctor_id` (1:N)
- `appointments.appointment_id` -> `appointment_status_history.appointment_id` (1:N)
- `appointments.appointment_id` -> `medical_records.appointment_id` (1:1)
- `users.user_id` -> `appointment_status_history.changed_by` (1:N)
- `users.user_id` -> `notifications.user_id` (1:N)
- `users.user_id` -> `audit_logs.user_id` (1:N)

## Normalization

- The design separates user identity from patient- and doctor-specific details.
- Appointment metadata is stored separately from status history and medical records.
- Department assignment uses a join table to avoid repeating doctor or department text values.
- This structure satisfies 3NF by removing repeating groups and transitive dependencies.
