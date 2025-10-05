-- Adiciona a coluna calc_state se não existir
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'budgets'
    AND COLUMN_NAME = 'calc_state'
);

SET @sql := IF(@col_exists = 0,
  'ALTER TABLE budgets ADD COLUMN calc_state ENUM("PENDENTE","ESTIMADO","FINAL") NOT NULL DEFAULT "PENDENTE"',
  'SELECT "budgets.calc_state já existe"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
