-- Nextfram Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS nextfram_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nextfram_db;

-- Admin Users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_fr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    description_fr TEXT NOT NULL,
    description_en TEXT NOT NULL,
    price VARCHAR(100),
    price_label_fr VARCHAR(100),
    price_label_en VARCHAR(100),
    icon VARCHAR(100),
    is_addon TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Portfolio
CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_fr VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    description_fr TEXT,
    description_en TEXT,
    category ENUM('photo','video','animation') NOT NULL,
    thumbnail VARCHAR(255),
    media_url VARCHAR(255),
    client_name VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Team
CREATE TABLE team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    role_fr VARCHAR(255) NOT NULL,
    role_en VARCHAR(255) NOT NULL,
    bio_fr TEXT,
    bio_en TEXT,
    photo VARCHAR(255),
    instagram_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(50) NOT NULL,
    service_id INT,
    booking_type ENUM('consultation','service') NOT NULL,
    preferred_date DATE,
    message TEXT,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT
);

-- Seed Data

-- Admin user: username=admin, password=nextfram2024
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Services
INSERT INTO services (title_fr, title_en, description_fr, description_en, price, price_label_fr, price_label_en, icon, is_addon, sort_order) VALUES
('Photographie Professionnelle', 'Professional Photography', 'Séances photo de haute qualité pour riads, boutiques hôtels et petites entreprises. Chaque image raconte une histoire.', 'High-quality photo shoots for riads, boutique hotels, and small businesses. Every image tells a story.', '1500 MAD', 'À partir de', 'Starting from', '📸', 0, 1),
('Production Vidéo & Reels', 'Video Production & Reels', 'Vidéos cinématographiques et reels Instagram qui captivent votre audience et augmentent votre visibilité en ligne.', 'Cinematic videos and Instagram reels that captivate your audience and boost your online visibility.', '2500 MAD', 'À partir de', 'Starting from', '🎬', 0, 2),
('Publicités Animées', 'Animated Ads', 'Créations visuelles animées pour vos campagnes publicitaires sur les réseaux sociaux. Design percutant, message clair.', 'Animated visual creations for your social media advertising campaigns. Impactful design, clear message.', '800 MAD', 'À partir de', 'Starting from', '✨', 0, 3),
('Gestion des Réseaux Sociaux', 'Social Media Management', 'Prise en charge complète de votre présence digitale : stratégie, création de contenu, planification et analyse.', 'Complete management of your digital presence: strategy, content creation, scheduling and analytics.', '3000 MAD', 'Par mois', 'Per month', '📱', 0, 4);

-- Portfolio items
INSERT INTO portfolio (title_fr, title_en, description_fr, description_en, category, thumbnail, client_name, is_featured, is_active) VALUES
('Riad El Bahia', 'Riad El Bahia', 'Séance photo complète pour ce riad de luxe au cœur de Marrakech.', 'Complete photo shoot for this luxury riad in the heart of Marrakech.', 'photo', '', 'Riad El Bahia', 1, 1),
('Campagne Ramadan', 'Ramadan Campaign', 'Série de reels animés pour la campagne Ramadan d\'un restaurant traditionnel.', 'Series of animated reels for a traditional restaurant Ramadan campaign.', 'animation', '', 'Restaurant Dar Zitoun', 1, 1),
('Boutique Hôtel Atlas', 'Atlas Boutique Hotel', 'Production vidéo cinématographique pour cet hôtel boutique au pied de l\'Atlas.', 'Cinematic video production for this boutique hotel at the foot of the Atlas mountains.', 'video', '', 'Atlas Boutique Hotel', 1, 1);

-- Team members
INSERT INTO team (full_name, role_fr, role_en, bio_fr, bio_en, instagram_url, sort_order) VALUES
('Youssef Ait Brahim', 'Directeur Créatif', 'Creative Director', 'Passionné par le storytelling visuel, Youssef dirige chaque projet avec une vision artistique unique et une attention aux détails exceptionnelle.', 'Passionate about visual storytelling, Youssef leads every project with a unique artistic vision and exceptional attention to detail.', 'https://instagram.com', 1),
('Salma Benali', 'Photographe & Vidéaste', 'Photographer & Videographer', 'Avec 5 ans d\'expérience dans la photographie de luxe au Maroc, Salma capture l\'essence de chaque lieu avec sensibilité et précision.', 'With 5 years of experience in luxury photography in Morocco, Salma captures the essence of every place with sensitivity and precision.', 'https://instagram.com', 2);

-- Settings
INSERT INTO settings (`key`, value) VALUES
('phone', '+212 639 797 751'),
('email', 'contact@nextfram.com'),
('instagram_url', 'https://instagram.com/nextfram'),
('facebook_url', 'https://facebook.com/nextfram'),
('whatsapp', '+212639797751'),
('logo_path', ''),
('showreel_path', ''),
('tagline_fr', 'Votre présence visuelle, réinventée.'),
('tagline_en', 'Your visual presence, reinvented.'),
('agency_name', 'Nextfram'),
('address', 'Marrakesh, Maroc'),
('hero_subtitle_fr', 'Agence créative spécialisée dans la photographie, la vidéo et le contenu digital pour les riads, hôtels boutiques et entreprises au Maroc.'),
('hero_subtitle_en', 'Creative agency specializing in photography, video and digital content for riads, boutique hotels and businesses across Morocco.');
