<?php
// Check permissions
if (!$g_user->hasPermission('plugin_interview_admin')) {
    echo "alert('".(getGS('You do not have the right to manage interviews.'))."');";
    exit;
}

$f_action = Input::Get('f_action', 'string');

switch ($f_action) {    
    case 'interviews_delete':
        $f_interviews = Input::Get('f_interviews', 'array');
        
        foreach ($f_interviews as $interview_id) {
            $Interview = new Interview($interview_id);
            $Interview->delete();   
        }
    break;
    
    case 'items_delete':
        $f_items = Input::Get('f_items', 'array');
        
        foreach ($f_items as $item_id) {
            $InterviewItem = new InterviewItem(null, $item_id);
            $InterviewItem->delete();   
        }
    break;
    
    case 'interviews_setdraft':
    case 'interviews_setpending':
    case 'interviews_setpublic':
    case 'interviews_setoffline':
        $f_interviews = Input::Get('f_interviews', 'array');
        $status = substr($f_action, 14);
        
        foreach ($f_interviews as $interview_id) {
            $Interview = new Interview($interview_id);
            $Interview->setProperty('status', $status);   
        }
    break;
    
    case 'items_setdraft':
    case 'items_setpending':
    case 'items_setpublic':
    case 'items_setoffline':
        $f_items = Input::Get('f_items', 'array');
        $status = substr($f_action, 9);
        
        foreach ($f_items as $item_id) {
            $InterviewItem = new InterviewItem(null, $item_id);
            $InterviewItem->setProperty('status', $status);   
        }
    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
