ALTER TABLE UserTypes ADD COLUMN InitializeTemplateEngine ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE UserPerm ADD COLUMN InitializeTemplateEngine ENUM('N', 'Y') NOT NULL DEFAULT 'N';
