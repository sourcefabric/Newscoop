
-- Map setting
-- should be geo-points autofocused
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusDefault', '1');
-- maximal map zoom for map autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusMaxZoom','18');
-- map border spaces for autofocusing
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoFocusBorder','100');
-- css file to be included for map views
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAutoCSSFile','/javascript/geocoding/styles/map-info.css');


