<?php

function smarty_function_wobs_calendar($p_params, &$p_smarty)
{
    global $controller;

    $p_content = $controller->view->action(
        'index',
        'articleoftheday',
        'default'
    );

    return($p_content);
}

?>