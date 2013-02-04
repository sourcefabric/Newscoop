<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite debate_list block plugin
 *
 * Type:     block
 * Name:     debate_list
 * Purpose:  Create a list of available debates
 *
 * @param string
 *     $p_params
 * @param Smarty_Internal_Template
 *     $p_smarty
 * @param string
 *     $p_repeat
 *
 * @return
 *
 */
function smarty_function_debatevotes($p_params, &$p_smarty)
{
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');
    /* @var $campContext \CampContext */
    $html = '';

    if ($campContext->current_list && $campContext->current_list->current instanceof MetaDebate)
    {
        $debate = $campContext->current_list->current;
        /* @var $debate MetaDebate */
        $results = DebateVote::getResults($debate->getNr(), $debate->getLanguageId());

        if (!isset($p_params['date_format']))
        {
            switch($debate->getResultsTimeUnit())
        	{
        	    case 'daily' : $dformat = '%e.%m.%y'; break;
                case 'weekly' : $dformat = '%W-%y'; break;
        	    case 'monthly' : $dformat = '%b-%y'; break;
            }
        }
        else {
            $dformat = $p_params['date_format'];
        }

        $html .= '<div class="debate-results">';
	    foreach ( $results as $resitem)
	    {
	        $html .= '<div class="debate-result-item">';
	        $html .= '<div class="debate-result-item-values">';
	        foreach ($results as $result)
	        {
	            if (!is_array($result)) continue;
	            $html .= '<div class="debate-result-item-answer-value"'
	            	   . "style='height:'".($percentage = number_format($result['value']*100/$results['total_count'], 2)) . "%'>"
	            	   . $percentage . '%'
	            	   . '</div>';

	        }
	        $html .= '<div class="bottom">'.strftime($p_params['date_format'], $results['time']).'</div>'
	               . '<div style="clear: both"></div>';
	    }
	    $html .= '</div>';

    };

    return $html;
}