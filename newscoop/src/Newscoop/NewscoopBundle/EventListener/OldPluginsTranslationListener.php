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

/**
 * Add old plugins translations
 */
class OldPluginsTranslationListener
{
    protected $translator;

    protected $cacheService;

    protected $pluginsService;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator, $cacheService, $pluginsService)
    {
        $this->translator = $translator;
        $this->cacheService = $cacheService;
        $this->pluginsService = $pluginsService;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $locale = $event->getRequest()->getLocale();
        $cacheKey = 'oldPlugins_translations_'.count($this->pluginsService->getEnabledPlugins());
        if ($this->cacheService->contains($cacheKey)) {
            $files = $this->cacheService->fetch($cacheKey);
        } else {
            $finder = new Finder();
            $extension = $locale.'.yml';
            $files = array();

            $finder->files()->in(__DIR__.'/../../../../plugins');
            $finder->files()->name('*.'.$locale.'.yml');

            foreach ($finder as $file) {
                $domain = substr($file->getFileName(), 0, -1 * strlen($extension) - 1);
                $files[$domain] = $file->getRealpath();
            }

            $this->cacheService->save($cacheKey, $files);
        }

        try {
            if (count($files) > 0) {
                $this->translator->addLoader('yaml', new YamlFileLoader());
                foreach ($files as $key => $file) {
                    $this->translator->addResource('yaml', $file, $locale, $key);
                }
            }
        } catch (\InvalidArgumentException $exception) {
            throw new \Exception('Plugins directory doesn\'t exist!');
        }
    }
}
