CREATE DATABASE IF NOT EXISTS rondi3d DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rondi3d;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  pass_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS estimates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(160) DEFAULT NULL,
  description TEXT,
  material ENUM('Indefinido','PLA','PETG','Resina') DEFAULT 'Indefinido',
  weight_g DECIMAL(10,2) NOT NULL DEFAULT 0,
  time_h_decimal DECIMAL(10,2) NOT NULL DEFAULT 0,
  price_final DECIMAL(12,2) NOT NULL DEFAULT 0,
  breakdown_json JSON NULL,
  status ENUM('Recebido','Em análise','Aprovado','Em produção','Concluído','Cancelado') NOT NULL DEFAULT 'Recebido',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_estimates_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS files (
  id INT AUTO_INCREMENT PRIMARY KEY,
  estimate_id INT NOT NULL,
  kind ENUM('photo') NOT NULL DEFAULT 'photo',
  path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime VARCHAR(100) NOT NULL,
  size_bytes INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_files_est FOREIGN KEY (estimate_id) REFERENCES estimates(id) ON DELETE CASCADE
) ENGINE=InnoDB;
