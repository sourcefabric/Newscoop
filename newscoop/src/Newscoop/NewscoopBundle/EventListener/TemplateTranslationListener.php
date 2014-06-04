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
    protected $translator;

    protected $cacheService;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator, $cacheService)
    {
        $this->translator = $translator;
        $this->cacheService = $cacheService;
    }

    /**
     * @param  GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $cacheKey = $this->cacheService->getCacheKey('templates_translations', 'templates');
        $templateTranslations = array();

        if ($this->cacheService->contains($cacheKey)) {
            $templateTranslations = $this->cacheService->fetch($cacheKey);
            foreach ($templateTranslations as $translation) {
                $this->translator->addResource('yaml', $translation[0], $translation[1], $translation[2]);
            }

            return;
        }

        $request = $event->getRequest();
        $locale = $request->getLocale();
        $filesystem = new Filesystem();
        $dir = __DIR__.'/../../../../themes/'.\CampSite::GetURIInstance()->getThemePath().'translations';

        if ($filesystem->exists($dir)) {
            $finder = new Finder();
            $this->translator->addLoader('yaml', new YamlFileLoader());
            $extension = $locale.'.yml';
            $finder->files()->in($dir);
            $finder->files()->name('*.'.$locale.'.yml');

            foreach ($finder as $file) {
                $domain = substr($file->getFileName(), 0, -1 * strlen($extension) - 1);
                $this->translator->addResource('yaml', $file->getRealpath(), $locale, $domain);
                $templateTranslations[] = array($file->getRealpath(), $locale, $domain);
            }
        }

        $this->cacheService->save($cacheKey, $templateTranslations);
    }
}
