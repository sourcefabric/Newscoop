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
    /**
     * Render template
     */
    public function _run()
    {
        $container = \Zend_Registry::get('container');
        $templatesService = $container->getService('newscoop.templates.service');
        $request = $container->getService('request');
        $language = $container->get('em')->getRepository('Newscoop\Entity\Language')->findOneByCode($request->getLocale());

        $params = $this->getVars();
        $params['view'] = $this;
        $templatesService->setVector(array(
            'publication' => $request->attributes->get('_newscoop_publication_metadata[alias][publication_id]', null, true),
            'language' => $language->getId(),
            'params' => json_encode(array(
                'request' => \Zend_Controller_Front::getInstance()->getRequest()->getParams()
            ))
        ));

        $file = array_shift(func_get_args());
        $templatesService->renderTemplate($file, $params);
    }

    /**
     * Add script path
     *
     * @param string $path
     */
    public function addPath($path)
    {
        $templatesService = \Zend_Registry::get('container')->getService('newscoop.templates.service');
        $templatesService->getSmarty()->addTemplateDir($path);
    }
}
