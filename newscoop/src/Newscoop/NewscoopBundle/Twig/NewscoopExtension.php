<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Twig;

class NewscoopExtension extends \Twig_Extension
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        if ($this->container->isScopeActive('request')) {
            $this->request = $this->container->get('request');
        }
    }

    public function getGlobals()
    {
        global $Campsite;

        $localeFromCookie = 'en';
        if ($this->request) {
            $localeFromCookie = $this->request->cookies->has('TOL_Language') == true ? $this->request->cookies->get('TOL_Language') : 'en';
        }

        return array(
            'Newscoop' => $Campsite,
            'NewscoopVersion' => new \CampVersion(),
            'SecurityToken' => \SecurityToken::GetToken(),
            'NewscoopUser' => $this->container->getService('user')->getCurrentUser(),
            'localeFromCookie' => $localeFromCookie
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('strpos', 'strpos'),
            new \Twig_SimpleFunction('getBreadcrumbsArray', array($this, 'getBreadcrumbsArray')),
            new \Twig_SimpleFunction('getReCaptchaImage', array($this, 'getReCaptchaImage')),
            new \Twig_SimpleFunction('renderHook', array($this, 'renderHook')),
            new \Twig_SimpleFunction('getSystemPref', "\Zend_Registry::get('container')->getService('system_preferences_service')->get"),
        );
    }

    public function getBreadcrumbsArray($currentMenuItem) {
        $manipulator = new \Knp\Menu\Util\MenuManipulator();
        return $manipulator->getBreadcrumbsArray($currentMenuItem);
    }

    public function getReCaptchaImage()
    {
        $fontsDirectory = __DIR__.'/../../../../include/captcha/';
        $aFonts = array(
            $fontsDirectory.'fonts/VeraBd.ttf', 
            $fontsDirectory.'fonts/VeraIt.ttf', 
            $fontsDirectory.'fonts/Vera.ttf'
        );
        $oVisualCaptcha = new \PhpCaptcha($aFonts, 200, 60);
        $oVisualCaptcha->Create(__DIR__.'/../../../../images/cache/recaptcha.png');

        return '/images/cache/recaptcha.png';
    }

    public function renderHook($hookName, $params = array())
    {
        echo $this->container->get('newscoop.plugins.service')
            ->renderPluginHooks($hookName, null, $params);
    }

    public function getName()
    {
        return 'newscoop_extension';
    }
}