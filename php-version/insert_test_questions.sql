-- Test sorusu ekle
-- Not: created_by değerini kendi admin user ID'niz ile değiştirin (genellikle 1)

INSERT INTO `olimpiyat_questions` (`question_text`, `image_path`, `created_by`, `created_at`) 
VALUES 
('Bir sayının karesi 144 ise, bu sayının küpü kaçtır?', NULL, 1, NOW()),
('İki sayının toplamı 15, çarpımı 56 ise bu sayılar kaçtır?', NULL, 1, NOW()),
('Bir üçgenin kenar uzunlukları 3, 4 ve 5 cm ise alanı kaç cm² dir?', NULL, 1, NOW());

-- Test cevapları ekle (opsiyonel)
-- Not: user_id değerini kendi user ID'niz ile değiştirin

INSERT INTO `olimpiyat_answers` (`question_id`, `answer_text`, `user_id`, `user_name`, `created_at`)
VALUES 
(1, 'Sayı 12 veya -12 olabilir. 12³ = 1728, (-12)³ = -1728', 1, 'Admin', NOW()),
(2, 'Sayılar 7 ve 8 dir. 7+8=15 ve 7×8=56', 1, 'Admin', NOW());
