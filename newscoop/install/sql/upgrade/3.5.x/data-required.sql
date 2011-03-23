-- change javascript directory references with the js for MapMarkerDirectory preference
-- only if the default is used otherwise the preference should be kept
UPDATE `SystemPreferences` SET `value` = '/js/geocoding/markers/' WHERE `varname` = 'MapMarkerDirectory' AND `value` = '/javascript/geocoding/markers/'
