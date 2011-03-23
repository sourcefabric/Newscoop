-- remove Campcaster related preferences
DELETE FROM `SystemPreferences` WHERE `varname` ='UseCampcasterAudioclips';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostName';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostPort';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCPath';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCFile';

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
SET path = SUBSTRING(path, LOCATE('extensions/', path) + LENGTH('extensions/'))
WHERE path LIKE '/%';

-- Delete missing Widget references
DELETE FROM `WidgetContext_Widget`
WHERE fk_widget_id NOT IN (
    SELECT id FROM `Widget`
);
