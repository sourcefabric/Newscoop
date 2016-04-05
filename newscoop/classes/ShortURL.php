<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');

class ShortURL
{
    public static function GetURL($publicationId, $languageId = null, $issueNo = null, $sectionNo = null, $articleNo = null)
    {
        $publicationService = \Zend_Registry::get('container')->getService('newscoop.publication_service');

        if ($publicationService->getPublication()) {
            if ($publicationService->getPublication()->getId() == $publicationId) {
                $publication = $publicationService->getPublication();
            } else {
                $em = \Zend_Registry::get('container')->getService('em');
                $publication = $em->getRepository('Newscoop\Entity\Publication')->findOneById($publicationId);

                if (!$publication) {
                    throw new \Exception('Publication does not exist.');
                }
            }
        } else {
            $em = \Zend_Registry::get('container')->getService('em');
            $publication = $em->getRepository('Newscoop\Entity\Publication')->findOneById($publicationId);

            if (!$publication) {
                throw new \Exception('Publication does not exist.');
            }
        }

        if (!isset($languageId)) {
            $languageId = $publication->getDefaultLanguage()->getId();
        }

        $uri = ShortURL::GetURI($publicationId, $languageId, $issueNo, $sectionNo, $articleNo);

        if (!is_string($uri) && PEAR::isError($uri)) {
            return $uri;
        }

        $scheme = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        return $scheme . $publication->getDefaultAlias()->getName() . $uri;
    }

    public static function GetURI($p_publicationId, $p_languageId, $p_issueNo = null, $p_sectionNo = null, $p_articleNo = null)
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        $languageObj = new Language($p_languageId);
        if (!$languageObj->exists()) {
            return new PEAR_Error($translator->trans('Language does not exist.'));
        }
        $uri = '/' . $languageObj->getCode() . '/';
        if (!is_null($p_issueNo) && is_null($p_articleNo)) {
            $issueObj = new Issue($p_publicationId, $p_languageId, $p_issueNo);
            if (!$issueObj->exists()) {
                return new PEAR_Error($translator->trans('Issue does not exist.'));
            }
            $uri .= $issueObj->getUrlName() . '/';
        }
        if (!is_null($p_sectionNo) && is_null($p_articleNo)) {
            $sectionObj = new Section($p_publicationId, $p_issueNo, $p_languageId, $p_sectionNo);
            if (!$sectionObj->exists()) {
                return new PEAR_Error($translator->trans('Section does not exist.'));
            }
            $uri .= $sectionObj->getUrlName() . '/';
        }
        if (!is_null($p_articleNo)) {
            $articleObj = new Article($p_languageId, $p_articleNo);

            if (!$articleObj->exists()) {
                return new PEAR_Error($translator->trans('Article does not exist.'));
            }
            $issueObj = new Issue($p_publicationId, $p_languageId, $articleObj->getIssueNumber());
            $sectionObj = new Section($p_publicationId, $articleObj->getIssueNumber(), $p_languageId,
            $articleObj->getSectionNumber());
            $uri .= $issueObj->getUrlName() . '/';
            $uri .= $sectionObj->getUrlName() . '/';
            $uri .= $articleObj->getUrlName() . '/';
        }
        return $uri;
    }
}

?>
