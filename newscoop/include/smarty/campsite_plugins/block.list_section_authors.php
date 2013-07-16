<?php
/**
 * @package Newscoop
 */

/**
 * Newscoop list_section_authors block plugin
 *
 * Type:     block
 * Name:     list_section_authors
 * Purpose:  Provides a list of section authors
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_list_section_authors($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $context = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $start = $context->next_list_start('SectionAuthorsList');
        $sectionAuthorsList = new SectionAuthorsList($start, $p_params);
        
        if ($sectionAuthorsList->isEmpty()) {error_log('current_before before');
            $context->setCurrentList($sectionAuthorsList, array());
            $context->resetCurrentList();
            $p_repeat = false;
            return null;
        }

        $context->setCurrentList($sectionAuthorsList, array('author'));
        $context->author = $context->current_section_authors_list->current;
        $p_repeat = true;
    } else {
        $context->current_section_authors_list->defaultIterator()->next();
        if (!is_null($context->current_section_authors_list->current)) {
            $context->author = $context->current_section_authors_list->current;
            $p_repeat = true;
        } else {
            $context->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}
?>
