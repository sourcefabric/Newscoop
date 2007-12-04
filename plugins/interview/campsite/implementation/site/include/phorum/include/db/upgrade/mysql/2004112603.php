<?php
if(!defined("PHORUM_ADMIN")) return;

$cid=phorum_db_mysql_connect();

// converting the custom-fields
$res=mysql_query("SELECT user_id,user_data FROM {$PHORUM['user_table']}",$cid);

while($row=mysql_fetch_assoc($res)) {
    $userdata=array('user_id'=>$row['user_id']);
    $user_data_new=array();
    $user_data_old=unserialize($row['user_data']);
    
    // converting meta-data to fields
    if(isset($user_data_old['show_signature']) && !empty($user_data_old['show_signature']))
        $userdata['show_signature']=$user_data_old['show_signature'];
            
    if(isset($user_data_old['email_notify']) && !empty($user_data_old['email_notify']))
        $userdata['email_notify']=$user_data_old['email_notify'];
        
    if(isset($user_data_old['tz_offset']) && !empty($user_data_old['tz_offset']))
        $userdata['tz_offset']=$user_data_old['tz_offset'];        
        
    if(isset($user_data_old['is_dst']) && !empty($user_data_old['is_dst']))
        $userdata['is_dst']=$user_data_old['is_dst'];

    if(isset($user_data_old['user_language']) && !empty($user_data_old['user_language']))
        $userdata['user_language']=$user_data_old['user_language'];

    if(isset($user_data_old['user_template']) && !empty($user_data_old['user_template']))
        $userdata['user_template']=$user_data_old['user_template'];    
        
    unset($user_data_old['user_template']);
    unset($user_data_old['user_language']);
    unset($user_data_old['is_dst']);
    unset($user_data_old['tz_offset']);
    unset($user_data_old['email_notify']);
    unset($user_data_old['show_signature']);
    
    // converting custom-fields now    
    if(is_array($user_data_old) && count($user_data_old)) {
        foreach($user_data_old as $old_key => $old_val) {
            $type=-1;
            // find out which ID that custom-field has
            foreach($PHORUM['PROFILE_FIELDS'] as $ctype => $cdata) {
                if($cdata['name'] == $old_key) {
                    $type=$ctype;
                    break;
                }
            }

            if($type != -1) { // store it only if we found it
                if( $old_val!=="" ) {
                    if(!is_array($old_val)) {
                        $user_data_new[$type] = substr($old_val,0,$PHORUM['PROFILE_FIELDS'][$type]['length']);
                    } else {
                        $user_data_new[$type] = $old_val;
                    }
                }
            }
        }
    }
    
    $userdata['user_data']=$user_data_new;
    phorum_user_save($userdata);
}
// remove old column
//mysql_query("ALTER TABLE {$PHORUM['user_newflags_table']} DROP newflags",$cid);

?>
