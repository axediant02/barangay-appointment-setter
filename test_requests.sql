-- Test Request Data for User ID 1

-- 1. Barangay Clearance
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 1, '2026-03-01 09:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Pending', NOW(), NOW());

-- 2. Indigency Certificate
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 2, '2026-03-02 10:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Approved', NOW(), NOW());

-- 3. Business Permit
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 3, '2026-03-03 11:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Rejected', NOW(), NOW());

-- 4. Residency Certificate
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 4, '2026-03-04 13:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Pending', NOW(), NOW());

-- 5. Good Moral Certificate
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 5, '2026-03-05 14:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Pending', NOW(), NOW());

-- 6. Community Tax Certificate
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 6, '2026-03-06 15:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Pending', NOW(), NOW());

-- 7. Barangay ID
INSERT INTO requests (user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, created_at, updated_at) VALUES
(1, 7, '2026-03-07 16:00:00', 'Juan Dela Cruz', 'Single', '1990-01-01', 'Block 1 Lot 2 Test St., Barangay San Jose', '09123456789', 'Pending', NOW(), NOW());
