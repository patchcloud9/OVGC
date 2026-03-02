-- Seed Users Table
-- Default password for all users: password123
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)

INSERT INTO users (name, email, password, role) VALUES
('Alice Johnson', 'alice@example.com', '$2y$12$aOljaUwUooeMee7iOEey5u0O3uAf.kT1dr8Sr9oB90ujmKEJ5piqi', 'admin'),
('Bob Smith', 'bob@example.com', '$2y$12$aOljaUwUooeMee7iOEey5u0O3uAf.kT1dr8Sr9oB90ujmKEJ5piqi', 'user'),
('Carol Williams', 'carol@example.com', '$2y$12$aOljaUwUooeMee7iOEey5u0O3uAf.kT1dr8Sr9oB90ujmKEJ5piqi', 'user'),
('David Brown', 'david@example.com', '$2y$12$aOljaUwUooeMee7iOEey5u0O3uAf.kT1dr8Sr9oB90ujmKEJ5piqi', 'user'),
('Eve Davis', 'eve@example.com', '$2y$12$aOljaUwUooeMee7iOEey5u0O3uAf.kT1dr8Sr9oB90ujmKEJ5piqi', 'admin');
