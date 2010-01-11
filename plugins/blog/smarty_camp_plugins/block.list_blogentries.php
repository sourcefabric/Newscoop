<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Campsite list_blogentries block plugin
 *
 * Type:     block
 * Name:     list_blogentries
 * Purpose:  Create a list of available blogentries
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_content
 * @param string
 *     $p_smarty
 * @param string
 *     $p_repeat
 *
 * @return
 *
 */
function smarty_block_list_blogentries($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('BlogEntriesList');
    	$blogEntriesList = new BlogEntriesList($start, $p_params);
    	$campContext->setCurrentList($blogEntriesList, array('blogentries'));
    }

    $currentBlogEntry = $campContext->current_blogentries_list->current;
    
    if (is_null($currentBlogEntry)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_blogentry_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->blogentry = $currentBlogEntry;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_blogentry_id', $currentBlogEntry->identifier);
    }

    if (isset($p_content)) {
		$html = $p_content;
        if ($p_repeat) {
            $campContext->current_blogentries_list->defaultIterator()->next();
            if (!is_null($campContext->current_blogentries_list->current)) {
                $campContext->blogentry = $campContext->current_blogentries_list->current;
                $campContext->url->set_parameter('f_blogentry_id', $campContext->current_blogentries_list->current->identifier);
            }
        }
    }

    return $html;
} // fn smarty_block_list_blogentries

?>