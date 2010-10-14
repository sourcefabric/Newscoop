<?php
camp_load_translation_strings("plugin_interview");

if (!SecurityToken::isValid()) {
    echo getGS('Invalid security token!');
    exit;
}

function camp_interview_permission_check($p_action)
{
    global $g_user, $call_script;

    // User role depend on path to this file. Tricky: moderator and guest folders are just symlink to admin files!
    if (strpos($call_script, '/interview/admin/') !== false && $g_user->hasPermission('plugin_interview_admin')) {
        $is_admin = true;
    }
    if (strpos($call_script, '/interview/moderator/') !== false && $g_user->hasPermission('plugin_interview_moderator')) {
        $is_moderator = true;
    }
    if (strpos($call_script, '/interview/guest/') !== false && $g_user->hasPermission('plugin_interview_guest')) {
        $is_guest = true;
    }

    switch ($p_action) {
        case 'interviews_delete':
            if ($is_admin) {
                return true;
            }
        break;

        case 'items_delete':
            if ($is_admin || $is_moderator) {
                return true;
            }
        break;

        case 'interviews_setdraft':
        case 'interviews_setpending':
        case 'interviews_setpublished':
        case 'interviews_setrejected':
            if ($is_admin) {
                return true;
            }
        break;

        case 'items_setdraft':
        case 'items_setpending':
        case 'items_setpublished':
        case 'items_setrejected':
            if ($is_admin || $is_moderator) {
                return true;
            }
        break;

        case 'item_move_up_rel':
        case 'item_move_down_rel':
        case 'item_move_abs':
            if ($is_admin || $is_moderator) {
                return true;
            }
        return false;

        case 'interview_move_up_rel':
        case 'interview_move_down_rel':
        case 'interview_move_abs':
            if ($is_admin || $is_moderator) {
                return true;
            }
        return false;
    }
}

$f_action = Input::Get('f_action', 'string');

if (!camp_interview_permission_check($f_action)) {
    echo getGS('You do not have the right to perform this action.');
    exit;
}

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
    case 'interviews_setpublished':
    case 'interviews_setrejected':
        $f_interviews = Input::Get('f_interviews', 'array');
        $status = substr($f_action, 14);

        foreach ($f_interviews as $interview_id) {
            $Interview = new Interview($interview_id);
            $Interview->setProperty('status', $status);
        }
    break;

    case 'items_setdraft':
    case 'items_setpending':
    case 'items_setpublished':
    case 'items_setrejected':
        $f_items = Input::Get('f_items', 'array');
        $status = substr($f_action, 9);

        foreach ($f_items as $item_id) {
            $InterviewItem = new InterviewItem(null, $item_id);
            $InterviewItem->setProperty('status', $status);
        }
    break;

    case 'item_move_up_rel':
    case 'item_move_down_rel':
        $f_items = Input::Get('f_items', 'array');
        list(,,$dir,) = explode('_', $f_action);

        foreach ($f_items as $item_id) {
            $InterviewItem = new InterviewItem(null, $item_id);
            $InterviewItem->positionRelative($dir);
        }

    break;

    case 'item_move_abs':
        $f_items = Input::Get('f_items', 'array');
        $f_new_pos = Input::Get('f_new_pos', 'int');

        foreach ($f_items as $item_id) {
            $InterviewItem = new InterviewItem(null, $item_id);
            $InterviewItem->positionAbsolute($f_new_pos);
        }

    break;

    case 'interview_move_up_rel':
    case 'interview_move_down_rel':
        $f_interviews = Input::Get('f_interviews', 'array');
        list(,,$dir,) = explode('_', $f_action);

        foreach ($f_interviews as $interview_id) {
            $Interview = new Interview($interview_id);
            $Interview->positionRelative($dir);
        }

    break;

    case 'interview_move_abs':
        $f_interviews = Input::Get('f_interviews', 'array');
        $f_new_pos = Input::Get('f_new_pos', 'int');

        foreach ($f_interviews as $interview_id) {
            $Interview = new Interview($interview_id);
            $Interview->positionAbsolute($f_new_pos);
        }

    break;
}

// Need to exit to avoid output of the menue.
exit;
?>
