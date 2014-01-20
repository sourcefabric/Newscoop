ALTER TABLE  `Images` DROP  `Caption` ;
DROP INDEX is_updated_storage ON Images;
ALTER TABLE
    Images CHANGE Id Id INT AUTO_INCREMENT NOT NULL,
    CHANGE Description Description VARCHAR(255) DEFAULT NULL,
    CHANGE Photographer Photographer VARCHAR(255) DEFAULT NULL,
    CHANGE Place Place VARCHAR(255) DEFAULT NULL,
    CHANGE Date Date VARCHAR(255) DEFAULT NULL,
    CHANGE ContentType ContentType VARCHAR(255) NOT NULL,
    CHANGE Location Location VARCHAR(255) NOT NULL,
    CHANGE URL URL VARCHAR(255) DEFAULT NULL,
    CHANGE ThumbnailFileName ThumbnailFileName VARCHAR(80) DEFAULT NULL,
    CHANGE ImageFileName ImageFileName VARCHAR(80) DEFAULT NULL,
    CHANGE LastModified LastModified DATETIME DEFAULT NULL,
    CHANGE TimeCreated TimeCreated DATETIME DEFAULT NULL,
    CHANGE Source Source VARCHAR(255) DEFAULT NULL,
    CHANGE Status Status VARCHAR(255) NOT NULL,
    CHANGE is_updated_storage is_updated_storage INT NOT NULL,
    CHANGE photographer_url photographer_url VARCHAR(255) DEFAULT NULL
;
CREATE INDEX IDX_E7B3BB5C447C15B9 ON Images (UploadedByUser);
CREATE INDEX is_updated_storage ON Images (is_updated_storage);