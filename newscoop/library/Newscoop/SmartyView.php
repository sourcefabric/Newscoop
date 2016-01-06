<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 */
class SmartyView extends \Zend_View_Abstract
{
    /** @var Smarty */
    protected $smarty;

    /**
     */
    public function __construct()
    {
        $this->smarty = \CampTemplate::singleton();
    }

    /**
     * Render template
     */
    public function _run()
    {
        foreach ($this->getVars() as $key => $val) {
            $this->smarty->assign($key, $val);
        }

        $context = $this->smarty->context();
        $locale = \Zend_Controller_Front::getInstance()->getParam('locale');
        $em = \Zend_Registry::get('container')->getService('em');
        try {
            $language = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($locale);
            $context->language = new \MetaLanguage($language->getId());
        } catch (\Exception $e) {
            // Do nothing, default language will be used
        }

        $this->smarty->assign('view', $this);
        $this->smarty->assign('gimme', $context);

        $file = array_shift(func_get_args());
        $this->smarty->display($file);
    }

    /**
     * Add script path
     *
     * @param string $path
     */
    public function addPath($path)
    {
        $this->smarty->addTemplateDir($path);
    }
}
