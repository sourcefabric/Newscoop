<?php
/**
 * @package   Newscoop\NewscoopBundle
 * @author    Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.ú.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides url's for custom rss/atom feeds
 */
class FeedController extends Controller
{
    /**
     * @Route("/{languageCode}/feed/", name="newscoop_feed")
     * @Route("/{languageCode}/feed/{feedName}.{format}", requirements={"format" = "(rss|atom)"}, name="newscoop_feed_details")
     */
    public function renderFeedAction(Request $request, $languageCode = "en", $feedName = "default", $format = "rss")
    {
        $response = new Response();
        $templatesService = $this->container->get('newscoop.templates.service');
        $language = $this->container->get('em')->getRepository('Newscoop\Entity\Language')->findOneByCode($languageCode);
        if (!is_null($language)) {
            $templatesService->getSmarty()->context()->language = new \MetaLanguage($language->getId());
        }

        try {
            $templatesService->setVector(array(
                'params' => serialize(array($request->query->all(), $feedName, $format, $languageCode))
            ));
            $response->setContent($templatesService->fetchTemplate('_feed/'.$feedName.'.tpl'));
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/'.$format.'+xml; charset=UTF-8');
        } catch (\SmartyException $e) {
            $response->setContent($templatesService->fetchTemplate('404.tpl'));
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}
