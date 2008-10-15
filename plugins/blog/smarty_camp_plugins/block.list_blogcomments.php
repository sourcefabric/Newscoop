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
 * Campsite list_blogcomments block plugin
 *
 * Type:     block
 * Name:     list_blog
 * Purpose:  Create a list of available blogs
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
function smarty_block_list_blogcomments($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = 0;
    	$blogCommentsList = new BlogCommentsList($start, $p_params);
    	$campContext->setCurrentList($blogCommentsList, array('blogcomment'));
    }

    $currentBlogComment = $campContext->current_blogcomments_list->current;
    
    if (is_null($currentBlogComment)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_blogcomment_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->blogcomment = $currentBlogComment;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_blogcomment_id', $currentBlogComment->identifier);
    }

    if (isset($p_content)) {
		$html = $p_content;
        if ($p_repeat) {
            $campContext->current_blogcomments_list->defaultIterator()->next();
            if (!is_null($campContext->current_blogcomments_list->current)) {
                $campContext->blogcomment = $campContext->current_blogcomments_list->current;
                $campContext->url->set_parameter('f_blogcomment_id', $campContext->current_blogcomments_list->current->identifier);
            }
        }
    }

    return $html;
} // fn smarty_block_list_blogcomments

?>