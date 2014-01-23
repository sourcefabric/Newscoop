ALTER TABLE Attachments CHANGE
    id Id INT AUTO_INCREMENT NOT NULL,
    CHANGE fk_language_id fk_language_id INT DEFAULT NULL,
    CHANGE size_in_bytes size_in_bytes BIGINT DEFAULT NULL,
    CHANGE fk_user_id fk_user_id INT DEFAULT NULL,
    CHANGE last_modified last_modified DATETIME NOT NULL,
    CHANGE time_created time_created DATETIME NOT NULL,
    CHANGE Source Source VARCHAR(255) NOT NULL,
    CHANGE Status Status VARCHAR(255) NOT NULL
;
ALTER TABLE Attachments ADD CONSTRAINT FK_C158750178917F82 FOREIGN KEY (fk_description_id) REFERENCES Translations (id);
CREATE INDEX IDX_C1587501EB0716C0 ON Attachments (fk_language_id);
CREATE UNIQUE INDEX UNIQ_C158750178917F82 ON Attachments (fk_description_id);
CREATE UNIQUE INDEX UNIQ_C15875015741EEB9 ON Attachments (fk_user_id);

DROP INDEX phrase_language_index ON Translations;
DROP INDEX phrase_id ON Translations;
ALTER TABLE Translations DROP phrase_id, CHANGE id Id INT AUTO_INCREMENT NOT NULL, CHANGE fk_language_id fk_language_id INT NOT NULL;
CREATE INDEX IDX_DE86017FEB0716C0 ON Translations (fk_language_id);

ALTER TABLE ArticleAttachments DROP INDEX article_attachment_index;
ALTER TABLE ArticleAttachments ADD PRIMARY KEY (fk_article_number, fk_attachment_id);
ALTER TABLE ArticleAttachments CHANGE fk_article_number fk_article_number INT NOT NULL, CHANGE fk_attachment_id fk_attachment_id INT NOT NULL;