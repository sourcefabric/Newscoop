-- allow admin to manage notices
INSERT INTO `acl_rule` (`type`, `role_id`, `resource`, `action`)VALUES('allow', 1, 'notice', 'manage');

CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, publication_id INT DEFAULT NULL, language_id INT DEFAULT NULL, user_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, sub_title VARCHAR(255) NOT NULL, body VARCHAR(255) NOT NULL, zipcode VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, date DATETIME DEFAULT NULL, published DATETIME NOT NULL, unpublished DATETIME NOT NULL, priority SMALLINT DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_480D45C238B217A7 (publication_id), INDEX IDX_480D45C282F1BAF4 (language_id), INDEX IDX_480D45C2A76ED395 (user_id), PRIMARY KEY(id)) ENGINE = InnoDB;
CREATE TABLE notice_noticecategory (notice_id INT NOT NULL, noticecategory_id INT NOT NULL, INDEX IDX_B83B5C377D540AB (notice_id), INDEX IDX_B83B5C379827FAB8 (noticecategory_id), PRIMARY KEY(notice_id, noticecategory_id)) ENGINE = InnoDB;
CREATE TABLE notice_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(128) NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, root INT DEFAULT NULL, UNIQUE INDEX UNIQ_5D1C5227989D9B62 (slug), INDEX IDX_5D1C5227727ACA70 (parent_id), PRIMARY KEY(id)) ENGINE = InnoDB;
CREATE TABLE notice_conf (id INT AUTO_INCREMENT NOT NULL, conf_name VARCHAR(64) NOT NULL, options LONGTEXT NOT NULL COMMENT '(DC2Type:array)', PRIMARY KEY(id)) ENGINE = InnoDB;

ALTER TABLE notice_noticecategory ADD CONSTRAINT FK_B83B5C377D540AB FOREIGN KEY (notice_id) REFERENCES notice(id) ON DELETE CASCADE;
ALTER TABLE notice_noticecategory ADD CONSTRAINT FK_B83B5C379827FAB8 FOREIGN KEY (noticecategory_id) REFERENCES notice_category(id) ON DELETE CASCADE;
ALTER TABLE notice_category ADD CONSTRAINT FK_5D1C5227727ACA70 FOREIGN KEY (parent_id) REFERENCES notice_category(id) ON DELETE SET NULL;

-- the following foreign keys do not work because the referenced tables are not innoDB
-- ALTER TABLE notice ADD CONSTRAINT FK_480D45C238B217A7 FOREIGN KEY (publication_id) REFERENCES Publications(Id);
-- ALTER TABLE notice ADD CONSTRAINT FK_480D45C282F1BAF4 FOREIGN KEY (language_id) REFERENCES Languages(Id);
-- ALTER TABLE notice ADD CONSTRAINT FK_480D45C2A76ED395 FOREIGN KEY (user_id) REFERENCES liveuser_users(Id);