-- Seed Menu Items
-- Initial site navigation items

INSERT INTO menu_items (title, url, icon, parent_id, display_order, visibility, is_active, is_system) VALUES
('Home', '/', 'fas fa-home', NULL, 1, 'public', 1, 0),
('About', '/about', NULL, NULL, 2, 'public', 1, 0),
('Gallery', '/gallery', 'fas fa-images', NULL, 3, 'public', 1, 0),
('Contact', '/contact', 'fas fa-envelope', NULL, 4, 'public', 1, 0);

-- Test Menu dropdown
INSERT INTO menu_items (title, url, icon, parent_id, display_order, visibility, is_active, is_system) VALUES
('Test Menu', '#', NULL, NULL, 5, 'public', 1, 0);

SET @test_menu_id = LAST_INSERT_ID();

INSERT INTO menu_items (title, url, icon, parent_id, display_order, visibility, is_active, is_system) VALUES
('Test One', '/', NULL, @test_menu_id, 1, 'public', 1, 0),
('Test Two', '/', NULL, @test_menu_id, 2, 'public', 1, 0),
('Test Three', '/', NULL, @test_menu_id, 3, 'public', 1, 0);
