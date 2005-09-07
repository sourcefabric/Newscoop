BEGIN;

INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (154, 'Duplicate section', 'N', 1);
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES (155, 'Duplicate article', 'N', 1);

DELETE FROM UserTypes WHERE Name = 'Reader';
DELETE FROM UserTypes WHERE Name = 'Administrator';
DELETE FROM UserTypes WHERE Name = 'Editor';
DELETE FROM UserTypes WHERE Name = 'Chief Editor';
INSERT INTO UserTypes VALUES ('Reader','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N');
INSERT INTO UserTypes VALUES ('Administrator','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','N','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');
INSERT INTO UserTypes VALUES ('Editor','N','N','N','N','N','N','N','Y','Y','Y','Y','Y','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','Y','N','N','N','N','N','Y','Y','N','N','N','Y','Y','Y','N','Y','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y');
INSERT INTO UserTypes VALUES ('Chief Editor','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N','Y','Y','N','N','Y','Y','N','N','Y','N','Y','Y','N','Y','Y','Y','N','Y','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y');

UPDATE UserPerm SET EditorImage = 'Y', EditorTextAlignment = 'Y', EditorFontColor = 'Y', EditorFontSize = 'Y', EditorFontFace = 'Y', EditorLink = 'Y', EditorSubhead = 'Y', EditorBold = 'Y', EditorItalic = 'Y', EditorUnderline = 'Y', EditorUndoRedo = 'Y', EditorCopyCutPaste = 'Y';

COMMIT;
