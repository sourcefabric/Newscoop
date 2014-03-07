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

        $this->smarty->assign('view', $this);
        $this->smarty->assign('gimme', $this->smarty->context());

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
