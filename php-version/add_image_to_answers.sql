-- Add image_path column to olimpiyat_answers table
ALTER TABLE `olimpiyat_answers` 
ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `answer_text`;
