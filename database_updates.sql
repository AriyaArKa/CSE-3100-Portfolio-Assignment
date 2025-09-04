-- Database Updates for Portfolio
-- Run these SQL commands to add image support to existing achievements table
-- Add image column to achievements table
ALTER TABLE achievements
ADD COLUMN image VARCHAR(255) NULL
AFTER description;
-- Optional: Add some sample data with images
-- UPDATE achievements SET image = 'images/achievement1.jpg' WHERE id = 1;
-- UPDATE achievements SET image = 'images/achievement2.png' WHERE id = 2;
-- You can also add an images directory path column if needed
-- ALTER TABLE achievements ADD COLUMN image_path VARCHAR(500) NULL AFTER image;