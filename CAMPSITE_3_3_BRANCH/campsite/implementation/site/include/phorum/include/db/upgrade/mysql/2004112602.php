<?php
if(!defined("PHORUM_ADMIN")) return;
/*
ALTER TABLE {$PHORUM['user_table']} ADD `show_signature` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `email_notify` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `tz_offset` TINYINT( 2 ) DEFAULT NULL ,
ADD `is_dst` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `user_language` VARCHAR( 100 ) NOT NULL ,
ADD `user_template` VARCHAR( 100 ) NOT NULL ;

create additional table for custom-fields

*/


// converting custom-field settings
if(!isset($PHORUM['PROFILE_FIELDS']['num_fields'])) {
    $new_profile_fields=array();
    foreach($PHORUM['PROFILE_FIELDS'] as $id => $name) {
        $new_profile_fields[$id]=array('name'=>$name,'length'=>255,'html_disabled'=>0);   
    }
    
    $new_profile_fields['num_fields']=count($new_profile_fields);
    $PHORUM['PROFILE_FIELDS']=$new_profile_fields;
    // saving them
    phorum_db_update_settings(array('PROFILE_FIELDS'=>$new_profile_fields));
}
?>