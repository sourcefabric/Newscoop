<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

require_once($GLOBALS['g_campsiteDir'] . '/admin-files/lib_campsite.php');

/**
 * Campsite Map function plugin
 *
 * Type:     function
 * Name:     count
 * Purpose:  Triggers a statistics counting request
 *
 * @param array
 *     $p_params List of parameters from template
 * @param object
 *     $p_smarty Smarty template object
 *
 * @return
 string     string The html content
 */
function smarty_function_count($p_params, &$p_smarty)
{
    global $Campsite;

    $campsite = $p_smarty->getTemplateVars('gimme');

    $content = '';
    {

        $art_number = 0;
        $art_language_num = 0;
        $art_language_code = '';

        if (isset($p_params['article']) && is_numeric($p_params['article'])) {
            $art_number = $p_params['article'];
        }

        if (isset($p_params['language'])) {
            $langs = array();
            if (is_numeric($p_params['language'])) {
                $langs = \Language::GetLanguages($p_params['language']);
            }
            else {
                $langs = \Language::GetLanguages(null, $p_params['language']);
            }
            if (!isset($langs[0])) {
                return ''; // 'no lang'
            }
            $art_language_obj = $langs[0];
            $art_language_num = $art_language_obj->getLanguageId();
            $art_language_code = $art_language_obj->getCode();
        }

        if ((!$art_number) || (!$art_language_num)) {
            $meta_article = $campsite->article;
            if ($meta_article->defined) {
                if (!$art_number) {
                    $art_number = $meta_article->number;
                }
                if (!$art_language_num) {
                    $art_language_meta = $meta_article->language;
                    $art_language_num = $art_language_meta->number;
                    $art_language_code = $art_language_meta->code;
                }
            }
        }

        if (!$art_language_num) {
            $art_language_meta = $campsite->language;
            $art_language_num = $art_language_meta->number;
            $art_language_code = $art_language_meta->code;
        }

        if ((!$art_number) || (!$art_language_num)) {
            return ''; // 'no art_num or lang'
        }

        $article = new \Article($art_language_num, $art_number);
        if (!$article->exists()) {
            return ''; // 'no art'
        }

        try {
                $requestObjectId = $article->getProperty('object_id');
                $updateArticle = empty($requestObjectId);

                $objectType = new \ObjectType('article');
                $object_type_id = $objectType->getObjectTypeId();

                if ($updateArticle) {
                    $requestObject = new \RequestObject($requestObjectId);
                    if (!$requestObject->exists()) {
                        $requestObject->create(array('object_type_id'=>$objectType->getObjectTypeId()));
                        $requestObjectId = $requestObject->getObjectId();
                    }
                    $article->setProperty('object_id', $requestObjectId);
                }

                // statistics shall be only gathered if the site admin set it on (and not for editor previews)
                if (!$campsite->preview) {
                    $stat_web_url = $Campsite['WEBSITE_URL'];
                    if ('/' != $stat_web_url[strlen($stat_web_url)-1]) {
                        $stat_web_url .= '/';
                    }
                    $article_number = $article->getProperty('Number');
                    $name_spec = '_' . $article_number . '_' . $art_language_code;

                    $content .= \Statistics::JavaScriptTrigger(array('name_spec' => $name_spec, 'object_type_id' => $object_type_id, 'request_object_id' => $requestObjectId));
                }
        } catch (\Exception $ex) {
                return '';
        }
    }

    return $content;

} // fn smarty_function_count

?>
