UPDATE Authors SET first_name=TRIM(first_name), last_name=TRIM(last_name);
UPDATE Authors SET first_name=replace(replace(replace(first_name,'  ',' __|__'),'__|__ ',''),'__|__','') WHERE 0 < LOCATE('  ', first_name);
UPDATE Authors SET last_name=replace(replace(replace(last_name,'  ',' __|__'),'__|__ ',''),'__|__','') WHERE 0 < LOCATE('  ', last_name);
