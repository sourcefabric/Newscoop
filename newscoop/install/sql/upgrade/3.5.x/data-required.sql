-- fix CodePage values
UPDATE `Languages` SET `CodePage` = 'ko' WHERE `Name` = 'Korean';
UPDATE `Languages` SET `CodePage` = 'be' WHERE `Name` = 'Belarus';
UPDATE `Languages` SET `CodePage` = 'ka' WHERE `Name` = 'Georgian';


-- Map setting
-- should be geo-points autofocused
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusDefault', '1');
-- maximal map zoom for map autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusMaxZoom','10');
-- map border spaces for autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusBorder','50');
-- css file to be included for map views
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoCSSFile','/js/geocoding/styles/map-info.css');

-- set lesser default sizes for location popups and videos
UPDATE SystemPreferences SET value = 200 WHERE varname = "MapPopupWidthMin" AND value = 300;
UPDATE SystemPreferences SET value = 150 WHERE varname = "MapPopupHeightMin" AND value = 200;
UPDATE SystemPreferences SET value = 320 WHERE varname = "MapVideoWidthYouTube" AND value = 425;
UPDATE SystemPreferences SET value = 240 WHERE varname = "MapVideoHeightYouTube" AND value = 350;
UPDATE SystemPreferences SET value = 320 WHERE varname = "MapVideoWidthVimeo" AND value = 400;
UPDATE SystemPreferences SET value = 180 WHERE varname = "MapVideoHeightVimeo" AND value = 225;
UPDATE SystemPreferences SET value = 320 WHERE varname = "MapVideoWidthFlash" AND value = 425;
UPDATE SystemPreferences SET value = 240 WHERE varname = "MapVideoHeightFlash" AND value = 350;
UPDATE SystemPreferences SET value = 320 WHERE varname = "MapVideoWidthFlv" AND value = 300;
UPDATE SystemPreferences SET value = 240 WHERE varname = "MapVideoHeightFlv" AND value = 280;
-- remove poi unfilled descriptions and try to copy from perex if was filled
UPDATE LocationContents SET poi_text = "" WHERE poi_text = "fill in the point description (*)";
UPDATE LocationContents SET poi_text = "" WHERE poi_text = "fill in the point description";
UPDATE LocationContents SET poi_text = poi_perex WHERE poi_text = "" AND poi_perex != "";

-- remove Campcaster related preferences
DELETE FROM `SystemPreferences` WHERE `varname` ='UseCampcasterAudioclips';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostName';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostPort';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCPath';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCFile';

-- Index on icons, they can be used for multi-map constraints
ALTER TABLE MapLocations ADD INDEX map_locations_poi_style_idx(poi_style(64));

-- Fix references to duplicated widgets
UPDATE `WidgetContext_Widget`
SET fk_widget_id = (
    SELECT w1.id FROM `Widget` w1, `Widget` w2 WHERE w1.class = w2.class AND w1.id > w2.id AND w2.id = fk_widget_id
)
WHERE fk_widget_id IN (
    SELECT w2.id FROM `Widget` w1, `Widget` w2 WHERE w1.class = w2.class AND w1.id > w2.id
);

-- Delete duplicated widgets
DELETE FROM `Widget`
WHERE ID IN (
    -- Must be materialized not to fail
    SELECT ID FROM (
        SELECT w2.id FROM `Widget` w1, `Widget` w2 WHERE w1.class = w2.class AND w1.id > w2.id
    ) as tmp
);

-- Change Widget absolute paths to relative
UPDATE `Widget`
SET path = SUBSTRING(path, LOCATE('extensions', path) + LENGTH('extensions') + 1)
WHERE path LIKE '/%' OR path LIKE '_:\\\\%';

-- Delete missing Widget references
DELETE FROM `WidgetContext_Widget`
WHERE fk_widget_id NOT IN (
    SELECT id FROM `Widget`
);

-- change javascript directory references with the js for MapMarkerDirectory preference
-- only if the default is used otherwise the preference should be kept
UPDATE `SystemPreferences` SET `value` = '/js/geocoding/markers/' WHERE `varname` = 'MapMarkerDirectory' AND `value` = '/javascript/geocoding/markers/';

-- Set the Sections new id field
UPDATE `Sections` s
JOIN `Issues` AS i ON
i.`IdPublication` = s.`IdPublication` AND i.`Number` = s.`NrIssue` AND i.`IdLanguage` = s.`IdLanguage`
SET `fk_issue_id` = i.`id`;

-- Remove the sync phorum user from sql 
DELETE FROM `liveuser_grouprights` WHERE `right_id` IN (SELECT `right_id` FROM `liveuser_rights` WHERE `right_define_name` = 'SyncPhorumUsers');
DELETE FROM `liveuser_userrights` WHERE `right_id` IN (SELECT `right_id` FROM `liveuser_rights` WHERE `right_define_name` = 'SyncPhorumUsers');
DELETE FROM `liveuser_rights` WHERE `right_define_name` = 'SyncPhorumUsers';

-- whether we shall use internal statistics on article reading
INSERT INTO SystemPreferences (varname, value) VALUES ('CollectStatistics', 'Y');

-- clean the Templates table
DELETE FROM Templates;

system php ./acl.php
system php ./transfer_phorum.php
system php ./javascript_js_cleanup.php

