<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Image block
 *
 * @param array $params
 * @param string $content
 * @param Smarty_Internal_Template $smarty
 * @param bool $repeat
 * @return void
 */
function smarty_block_image(array $params, $content, Smarty_Internal_Template $smarty, $repeat)
{
    if (!$repeat) {
        return $content;
    }

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
    if ($articleRendition === null) {
        $repeat = false;
        return;
    }

    if (array_key_exists('width', $params) && array_key_exists('height', $params)) {
        $preview = $articleRendition->getRendition()->getPreview($params['width'], $params['height']);
        $thumbnail = $preview->getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));
    } else {
        $thumbnail = $articleRendition->getRendition()->getThumbnail($articleRendition->getImage(), Zend_Registry::get('container')->getService('image'));
    }

    $smarty->assign('image', (object) array(
        'src' => Zend_Registry::get('view')->url(array('src' => $thumbnail->src), 'image', true, false),
        'width' => $thumbnail->width,
        'height' => $thumbnail->height,
        'caption' => $articleRendition->getImage()->getCaption(),
        'photographer' => $articleRendition->getImage()->getPhotographer(),
    ));
}
