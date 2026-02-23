-- Seed Logs Table

INSERT INTO logs (level, message, context) VALUES
('info', 'Application started', '{"environment": "development", "version": "1.0"}'),
('info', 'User logged in', '{"user_id": 1, "ip": "192.168.1.1"}'),
('warning', 'Slow query detected', '{"query_time": 2.5, "query": "SELECT * FROM large_table"}'),
('error', 'Database connection failed', '{"host": "localhost", "database": "myapp"}'),
('info', 'Email sent successfully', '{"to": "alice@example.com", "subject": "Welcome"}');
