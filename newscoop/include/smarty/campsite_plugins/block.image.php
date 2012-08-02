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
function smarty_block_image(array $params, $content, Smarty_Internal_Template $smarty, &$repeat)
{
    if (!$repeat) {
        $smarty->assign('image', null);
        return $content;
    }

    if (!array_key_exists('rendition', $params)) {
        throw new \InvalidArgumentException("Unknown rendition");
    }

    $article = $smarty->getTemplateVars('gimme')->article;
    if (!$article) {
        throw new \RuntimeException("Not in article context.");
    }

    $width = array_key_exists('width', $params) ? (int) $params['width'] : null;
    $height = array_key_exists('height', $params) ? (int) $params['height'] : null;
    try {
        $image = Zend_Registry::get('container')->getService('image.rendition')->getArticleRenditionImage($article->number, $params['rendition'], $width, $height);
        if (!$image) {
            throw new \Exception("No image");
        }
    } catch (Exception $e) {
        $smarty->assign('image', false);
        $repeat = false;
        return;
    }

    $image['src'] = Zend_Registry::get('view')->url(array('src' => $image['src']), 'image', true, false);
    $image['original']->src = Zend_Registry::get('view')->url(array('src' => $image['original']->src), 'image', true, false);
    $image['caption'] = Zend_Registry::get('container')->getService('image')->getCaption($image['id'], $article->number, $article->language->number);
    $smarty->assign('image', (object) $image);
}
