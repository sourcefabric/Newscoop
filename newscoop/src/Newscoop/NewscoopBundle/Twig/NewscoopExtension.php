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
            new \Twig_SimpleFunction('loadTranslationStrings', 'camp_load_translation_strings'),
            new \Twig_SimpleFunction('getGS', 'getGS'),
            new \Twig_SimpleFunction('strpos', 'strpos'),
        );
    }

    public function strpos($text, $lookFor) {
        return strpos($text, $lookFor);
    }

    public function getGS()
    {
        $args = func_get_args();
        require_once( __DIR__ . '/../../../../admin-files/localizer/Localizer.php');

        return call_user_func_array('getGS', $args);
    }

    public function getName()
    {
        return 'newscoop_extension';
    }
}