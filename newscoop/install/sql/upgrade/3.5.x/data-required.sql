-- remove Campcaster related preferences
DELETE FROM `SystemPreferences` WHERE `varname` ='UseCampcasterAudioclips';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostName';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterHostPort';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCPath';
DELETE FROM `SystemPreferences` WHERE `varname` ='CampcasterXRPCFile';
