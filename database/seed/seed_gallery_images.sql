-- Seed data for gallery_images table
-- Sample gallery images for testing (Note: actual image files would need to be uploaded)

-- This is a placeholder - in production, use the upload form to add real images
-- For testing purposes, you can manually add image files to /public/uploads/gallery/
-- and then run this seed to create database entries

INSERT INTO gallery_images (title, description, price_type, price_amount, prints_available, prints_url, filename, file_path, uploaded_by) VALUES
('Sample Image 1', 'This is a placeholder for the first gallery image', 'hide', NULL, 0, NULL, 'sample1.jpg', '/uploads/gallery/sample1.jpg', 1),
('Sample Image 2', 'This is a placeholder for the second gallery image', 'hide', NULL, 0, NULL, 'sample2.jpg', '/uploads/gallery/sample2.jpg', 1);

-- Note: Before running this seed, ensure:
-- 1. The gallery_images table exists (run 08_create_gallery_images_table.sql)
-- 2. At least one user exists with id=1 (run 01_seed_users.sql)
-- 3. Consider creating actual image files or removing these placeholders after testing
