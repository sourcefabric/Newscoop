<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Smarty;

use NoiseLabs\Bundle\SmartyBundle\SmartyEngine as BaseEngine;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * SmartyEngine is an engine able to render Smarty templates.
 *
 * This class is heavily inspired by \Twig_Environment.
 * See {@link http://twig.sensiolabs.org/doc/api.html} for details about \Twig_Environment.
 *
 * Thanks to Symfony developer Christophe Coevoet (@stof) for a carefully code
 * review of this bundle.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class SmartyEngine extends BaseEngine
{
	/**
     * Constructor.
     *
     * @param \Smarty                     $smarty    A \Smarty instance
     * @param ContainerInterface          $container A ContainerInterface instance
     * @param TemplateNameParserInterface $parser    A TemplateNameParserInterface instance
     * @param LoaderInterface             $loader    A LoaderInterface instance
     * @param array                       $options   An array of \Smarty properties
     * @param GlobalVariables|null        $globals   A GlobalVariables instance or null
     * @param LoggerInterface|null        $logger    A LoggerInterface instance or null
     */
    public function __construct(\Smarty $smarty, ContainerInterface $container,
    TemplateNameParserInterface $parser, LoaderInterface $loader, array $options,
    GlobalVariables $globals = null, LoggerInterface $logger = null)
    {
        parent::__construct($smarty, $container, $parser, $loader, $options, $globals, $logger);

        $uri = \CampSite::GetURIInstance();
        $themePath = $uri->getThemePath();

        $view = new \Newscoop\SmartyView();
        $view
            ->addScriptPath(APPLICATION_PATH . '/views/scripts/')
            ->addScriptPath(realpath(APPLICATION_PATH . '/../themes/' . $themePath));

        $view->addPath(realpath(APPLICATION_PATH . '/../themes/' . $themePath));

        $this->smarty->assign('view', $view);
       	$this->smarty->assign('gimme', new \CampContext());
        $this->smarty->addTemplateDir(APPLICATION_PATH . '/../themes/');
        $this->smarty->addTemplateDir(APPLICATION_PATH . \CampTemplate::SCRIPTS);
        $this->smarty->addTemplateDir(array('SystemTemplates' => APPLICATION_PATH . '/../themes/unassigned/system_templates/'));
        $this->smarty->addTemplateDir(array('CurrentTheme' => realpath(APPLICATION_PATH . '/../themes/' . $themePath)));
        $this->smarty->addTemplateDir(array('NewscoopScripts' => APPLICATION_PATH . '/views/scripts/'));

        $this->smarty->addPluginsDir(array_merge(
            array(APPLICATION_PATH . \CampTemplate::PLUGINS),
            \CampTemplate::getPluginsPluginsDir()
        ));
    }
}