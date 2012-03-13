
-- insert the initial version valeus
INSERT INTO Versions (name, version) VALUES (last_db_version) VALUES ('3.6.x') ON DUPLICATE KEY UPDATE;
INSERT INTO Versions (name, version) VALUES (last_db_roll) VALUES ('2012-01-01') ON DUPLICATE KEY UPDATE;


