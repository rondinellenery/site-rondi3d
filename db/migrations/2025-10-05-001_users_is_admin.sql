-- Adiciona a coluna is_admin se não existir
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'is_admin'
);

SET @sql := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0',
  'SELECT "users.is_admin já existe"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
