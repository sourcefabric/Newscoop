
-- Map setting
-- should be geo-points autofocused
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusDefault', '1');
-- maximal map zoom for map autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusMaxZoom','18');
-- map border spaces for autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusBorder','100');
-- css file to be included for map views
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoCSSFile','/javascript/geocoding/styles/map-info.css');

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
    SELECT ID FROM ( -- Must be materialized not to fail
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
