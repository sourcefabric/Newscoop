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
    private $translator;

    private $cacheService;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator, $cacheService)
    {
        $this->translator = $translator;
        $this->cacheService = $cacheService;
    }

    public function onRequest(GetResponseEvent $event)
    {

        $request = $event->getRequest();
        $locale = $request->getLocale();
        $finder = new Finder();
        $extension = $locale.'.yml';

        try {
            $finder->files()->in(__DIR__.'/../../../../plugins');
            $finder->files()->name('*.'.$locale.'.yml');
            $cacheKey = 'oldPlugins_translations_'.$finder->count();
            if ($this->cacheService->contains($cacheKey)) {
                $files = $this->cacheService->fetch($cacheKey);
            } else {
                $files = array();
                foreach ($finder as $file) {
                    $domain = substr($file->getFileName(), 0, -1 * strlen($extension) - 1);
                    $files[$domain] = $file->getRealpath();
                }

                $this->cacheService->save($cacheKey, $files);
            }

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
