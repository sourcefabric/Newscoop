<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Doctrine\ORM\EntityManager;
use Newscoop\Services\CacheService;

/**
 * Save locale cookie.
 */
class LocaleListener
{
    protected $cacheService;

    protected $em;

    public function __construct(CacheService $cacheService, EntityManager $em)
    {
        $this->cacheService = $cacheService;
        $this->em = $em;
    }

    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->request->has('login_language')) {
            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie('TOL_Language', $request->request->get('login_language')));
            $event->setResponse($response);
        }
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $pos = strpos($request->server->get('REQUEST_URI'), '/admin');
        $cookies = $request->cookies;

        if ($cookies->has('TOL_Language')) {
            $request->setLocale($cookies->get("TOL_Language"));
        }

        // Allow for overriding locale with get parameter
        if ($request->query->has('__set_lang')) {
            try {
                $language = $this->em->getRepository('Newscoop\Entity\Language')
                    ->findByRFC3066bis($request->query->get('__set_lang'), true);

                if ($language) {
                    $request->setLocale($language->getCode());

                    return;
                }
            } catch (\Exception $e) {}
        }

        // if it's not admin
        if ($pos === false) {
            // try to get locale from issue
            $issueMetadata = $request->attributes->get('_newscoop_issue_metadata');
            $issueLanguageCode = $issueMetadata['code_default_language'];
            if ($issueLanguageCode) {
                $request->setLocale($issueLanguageCode);

                return;
            }

            // try to get locale from publication but only if locale is not in url
            $publicationMetadata = $request->attributes->get('_newscoop_publication_metadata');
            $languageCode = $publicationMetadata['publication']['code_default_language'];
            $locale = $this->extractLocaleFromUri($request->getRequestUri());
            if (!$locale) {
                $request->setLocale($languageCode);

                return;
            }

            // get langauge from url and check if it's valid - if it's then set is as current locale
            $cacheKey = $this->cacheService->getCacheKey(array('resolver', $locale), 'language');
            if ($this->cacheService->contains($cacheKey)) {
                $language = $this->cacheService->fetch($cacheKey);
            } else {
                $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneBy(array(
                    'code' => $locale,
                ));

                $this->cacheService->save($cacheKey, $language);
            }

            $request->setLocale(!is_null($language) ? $language->getCode() : $languageCode);
        }
    }

    private function extractLocaleFromUri($requestUri)
    {
        if ($requestUri !== "/") {
            $requestUri = str_replace("?", "", $requestUri);
            $extractedUri = array_filter(explode("/", $requestUri));
            if (isset($extractedUri[1])) {
                return $extractedUri[1];
            }
        }
    }
}
