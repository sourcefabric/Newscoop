BEGIN;

UPDATE UserPerm SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';
UPDATE UserTypes SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';

COMMIT;
