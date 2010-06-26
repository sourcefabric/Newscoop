<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Campsite list_blog block plugin
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
function smarty_block_list_blogs($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('BlogsList');
    	$blogsList = new BlogsList($start, $p_params);
    	$campContext->setCurrentList($blogsList, array('blog'));
    }

    $currentBlog = $campContext->current_blogs_list->current;
    
    if (is_null($currentBlog)) {
	    $p_repeat = false;
	    $campContext->url->reset_parameter('f_blog_id');
	    $campContext->resetCurrentList();
    	return $html;
    } else {
        $campContext->blog = $currentBlog;
    	$p_repeat = true;
    	$campContext->url->set_parameter('f_blog_id', $currentBlog->identifier);
    }

    if (isset($p_content)) {
		$html = $p_content;
        if ($p_repeat) {
            $campContext->current_blogs_list->defaultIterator()->next();
            if (!is_null($campContext->current_blogs_list->current)) {
                $campContext->blog = $campContext->current_blogs_list->current;
                $campContext->url->set_parameter('f_blog_id', $campContext->current_blogs_list->current->identifier);
            }
        }
    }

    return $html;
} // fn smarty_block_list_blogs

?>