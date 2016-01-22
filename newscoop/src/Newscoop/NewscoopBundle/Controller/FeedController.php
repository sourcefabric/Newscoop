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
     * @Route("/feed/", name="newscoop_feed")
     * @Route("/feed/{feedName}.{_format}", requirements={"_format" = "(rss|atom)"}, name="newscoop_feed_details")
     */
    public function renderFeedAction(Request $request, $feedName = "default", $_format = "rss")
    {
        $templatesService = $this->get('newscoop.templates.service');

        $response = new Response();
        try {
            $templatesService->setVector(array(
                'params' => serialize(array($request->query->all(), $feedName, $_format))
            ));
            $response->setContent($templatesService->fetchTemplate('_feed/'.$feedName.'.tpl'));
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/'.$_format.'+xml; charset=UTF-8');
        } catch (\SmartyException $e) {
            $response->setContent($templatesService->fetchTemplate('404.tpl'));
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        }

        return $response;
    }
}
