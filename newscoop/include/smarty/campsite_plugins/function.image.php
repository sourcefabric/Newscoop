<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Get image
 *
 * @param array $params
 * @param Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_image(array $params, Smarty_Internal_Template $smarty)
{
    if (!array_key_exists('rendition', $params)) {
        throw new \InvalidArgumentException("Rendition not set");
    }

    $renditions = Zend_Registry::get('container')->getService('image.rendition')->getRenditions();
    if (!array_key_exists($params['rendition'], $renditions)) {
        throw new \InvalidArgumentException("Unknown rendition");
    }

    $article = $smarty->getTemplateVars('gimme')->article;
    if (!$article) {
        throw new \RuntimeException("Not in article context.");
    }

    $articleRenditions = $article->getRenditions();
    $articleRendition = $articleRenditions[$renditions[$params['rendition']]];

    if (array_key_exists('width', $params) && array_key_exists('height', $params)) {
        $preview = $articleRendition->getRendition()->getPreview($params['width'], $params['height']);
        $thumbnail = $preview->getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));
    } else {
        $thumbnail = $articleRendition->getRendition()->getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));
    }

    return sprintf('<img src="%s" width="%d" height="%d" alt="%s" />',
        Zend_Registry::get('view')->url(array('src' => $thumbnail->src), 'image', true, false),
        $thumbnail->width,
        $thumbnail->height,
        array_key_exists('alt', $params) ? $params['alt'] : ''
    );
}
