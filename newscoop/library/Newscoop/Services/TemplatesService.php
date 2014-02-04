<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 * Attachment service
 */
class TemplatesService
{
    /**
     * @var \CampTemplate
     */
    private $smarty;

    public function __construct()
    {
        $this->smarty = \CampTemplate::singleton();
        $this->smarty->assign('gimme', $this->smarty->context());
        $this->preconfigureSmarty();
    }

    public function fetchTemplate($file, $cache_id = null, $compile_id = null, $parent = null)
    {
        return $this->smarty->fetch($file, $cache_id, $compile_id, $parent, false);
    }

    public function getSmarty()
    {
        return $this->smarty;
    }

    private function preconfigureSmarty()
    {
        $uri = \CampSite::GetURIInstance();
        $this->smarty->addTemplateDir(realpath(APPLICATION_PATH . '/../themes/' . $uri->getThemePath()));

        // reverse templates dir order
        $this->smarty->setTemplateDir(array_reverse($this->smarty->getTemplateDir()));
    }
}
