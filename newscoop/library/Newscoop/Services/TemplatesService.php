<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 *  Templates service
 */
class TemplatesService
{
    /**
     * @var \CampTemplate
     */
    protected $smarty;

    public function __construct()
    {
        $this->smarty = \CampTemplate::singleton();
        $this->smarty->assign('gimme', $this->smarty->context());
        $this->preconfigureSmarty();
    }

    public function fetchTemplate($file, $params = array())
    {
        $this->assignParameters($params);

        return $this->smarty->fetch($file, null, null, null, false);
    }

    public function renderTemplate($file, $params = array())
    {
        $this->assignParameters($params);

        return $this->smarty->fetch($file, null, null, null, true);
    }

    public function getSmarty()
    {
        return $this->smarty;
    }

    private function assignParameters($params = array())
    {
        foreach ($params as $key => $value) {
            $this->smarty->assign($key, $value);
        }
    }

    private function preconfigureSmarty()
    {
        $uri = \CampSite::GetURIInstance();
        $this->smarty->addTemplateDir(realpath(APPLICATION_PATH . '/../themes/' . $uri->getThemePath()));
        $this->smarty->config_dir = (realpath(APPLICATION_PATH . '/../themes/' . $uri->getThemePath() . '_conf'));

        // reverse templates dir order
        $this->smarty->setTemplateDir(array_reverse($this->smarty->getTemplateDir()));
    }
}
