<?php

function smarty_function_wobs_calendar($p_params, &$p_smarty)
{
    global $controller;

    $wobs_commands = array();

    if (isset($p_params['date'])){
        $wobs_commands['date'] = $p_params['date'];
    }
    if (isset($p_params['navigation'])){
        $wobs_commands['navigation'] = $p_params['navigation'];
    }
    if (isset($p_params['view'])){
        $wobs_commands['view'] = $p_params['view'];
    }
    if (isset($p_params['firstDay'])){
        $wobs_commands['firstDay'] = $p_params['firstDay'];
    }
    if (isset($p_params['showDayNames'])){
        $wobs_commands['showDayNames'] = $p_params['showDayNames'];
    }
    if (isset($p_params['earliestMonth'])){
        $wobs_commands['earliestMonth'] = $p_params['earliestMonth'];
    }
    if (isset($p_params['latestMonth'])){
        $wobs_commands['latestMonth'] = $p_params['latestMonth'];
    }

    $p_content = $controller->view->action(
        'index',
        'articleoftheday',
        'default',
        $wobs_commands
    );

    return($p_content);
}

?>