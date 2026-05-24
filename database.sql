-- SISTEM PERMAINAN PIRAMID - VOUTEX KKJR
-- Adaptasi Drama Korea "Pyramid Game" untuk Kelab Keselamatan Jalan Raya

CREATE DATABASE IF NOT EXISTS vostex_kkjr;
USE votex_kkjr;

-- Jadual pengguna
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(10) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jadual jawatan
CREATE TABLE IF NOT EXISTS positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jadual calon
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_number VARCHAR(20) NOT NULL,
    candidate_name VARCHAR(100) NOT NULL,
    candidate_class VARCHAR(50) NOT NULL,
    candidate_image VARCHAR(255) DEFAULT 'default.jpg',
    position_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
);

-- Jadual undian
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    position_id INT NOT NULL,
    vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (user_id, position_id)
);

-- Admin lalai (telefon: 0000000000, kata laluan: admin123)
INSERT INTO users (full_name, phone_number, password, role) VALUES
('Pentadbir Utama', '0000000000', 'admin123', 'admin');

-- Jawatan contoh
INSERT INTO positions (position_name) VALUES
('Presiden Piramid'),
('Naib Presiden'),
('Setiausaha Utama'),
('Bendahari');

-- Calon contoh
INSERT INTO candidates (candidate_number, candidate_name, candidate_class, candidate_image, position_id) VALUES
('P001', 'Ahmad bin Ali', '5A Sains', 'default.jpg', 1),
('P002', 'Siti Nur Aisyah', '5B Sastera', 'default.jpg', 1),
('V001', 'Muhammad Irfan', '4A Teknik', 'default.jpg', 2),
('V002', 'Fatimah Zahra', '4B Ekonomi', 'default.jpg', 2),
('S001', 'Hassan bin Omar', '3A Perdagangan', 'default.jpg', 3),
('S002', 'Aina Safiya', '3B Accounts', 'default.jpg', 3),
('B001', 'Zain bin Ahmad', '2A Komersial', 'default.jpg', 4),
('B002', 'Nurul Ain binti Mahmud', '2B Marketing', 'default.jpg', 4);
