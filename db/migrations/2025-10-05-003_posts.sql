-- Cria tabela posts se não existir
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(190) NOT NULL,
  title VARCHAR(190) NOT NULL,
  thumb_url VARCHAR(255) NULL,
  excerpt TEXT NULL,
  body MEDIUMTEXT NOT NULL,
  published TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Garante UNIQUE em slug (se ainda não existir)
SET @idx_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'posts'
    AND INDEX_NAME = 'uq_posts_slug'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE posts ADD CONSTRAINT uq_posts_slug UNIQUE (slug)',
  'SELECT "uq_posts_slug já existe"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- (Opcional) FULLTEXT para buscas melhores (MySQL 5.6+ InnoDB)
-- Só crie se você **quiser** usar MATCH ... AGAINST:
-- SET @ft_exists := (
--   SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
--   WHERE TABLE_SCHEMA = DATABASE()
--     AND TABLE_NAME = 'posts'
--     AND INDEX_NAME = 'ft_posts'
-- );
-- SET @sql := IF(@ft_exists = 0,
--   'CREATE FULLTEXT INDEX ft_posts ON posts(title,excerpt,body)',
--   'SELECT "ft_posts já existe"');
-- PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
