<?php
/**
 */

/**
 * Newscoop thumbnail function
 *
 * @param array $params
 * @param mixed $smarty
 * @return Newscoop\Image\Thumbnail
 */
function smarty_function_thumbnail($params, $smarty)
{
    $renditions = Zend_Registry::get('container')->getService('image.rendition')->getRenditions();
    if (!array_key_exists($params['rendition'], $renditions)) {
        return;
    }

    $context = $smarty->getTemplateVars('gimme');
    $article = $context->article;
    $articleRenditions = Zend_Registry::get('container')->getService('image.rendition')->getArticleRenditions($article->number);
    $articleRendition = $articleRenditions[$renditions[$params['rendition']]];
    $thumbnail = $articleRendition->getRendition()->getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));
    echo $thumbnail->getImg(Zend_Registry::get('view'));
}
