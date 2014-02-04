<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Loads templates translations
 */
class TemplateTranslationListener
{
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function onRequest(GetResponseEvent $event) {

        $request = $event->getRequest();
        $locale = $request->getLocale();
        $this->translator->addLoader('yaml', new YamlFileLoader());
        $finder = new Finder();
        $filesystem = new Filesystem();
        $extension = $locale.'.yml';
        $dir = $_SERVER['DOCUMENT_ROOT'].'/themes/'.\CampSite::GetURIInstance()->getThemePath().'translations';

        if ($filesystem->exists($dir)) {
            $finder->files()->in($dir);
            $finder->files()->name('*.'.$locale.'.yml');

            foreach ($finder as $file) {
                $domain = substr($file->getFileName(), 0, -1 * strlen($extension) - 1);
                $this->translator->addResource('yaml', $file->getRealpath(), $locale, $domain);
            }
        }
    }
}
