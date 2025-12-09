ALTER TABLE reports ADD COLUMN data_json LONGTEXT NULL;




ALTER TABLE reports
  ADD COLUMN pdf_hash VARCHAR(128) NULL AFTER `hash`,
  ADD COLUMN hash_algo VARCHAR(32) NULL AFTER `pdf_hash`;
