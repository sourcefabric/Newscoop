<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Playlists controller.
 */
class PlaylistsController extends Controller
{
    /**
     * @Route("/admin/playlists/")
     * @Route("/admin/playlists/related/", options={"expose"=true}, name="newscoop_newscoop_playlists_related")
     */
    public function indexAction(Request $request)
    {
        $preferencesService = $this->get('preferences');
        $em = $this->get('em');
        $clientName = 'newscoop_'.$preferencesService->SiteSecretKey;
        $client = $em->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findOneByName($clientName);

        $relatedView = false;
        if ($request->get('_route') === "newscoop_newscoop_playlists_related") {
            $relatedView = true;
        }

        return $this->render('NewscoopNewscoopBundle:Playlists:index.html.twig', array(
            'clientId' => $client ? $client->getPublicId() : '',
            'relatedView' => $relatedView,
        ));
    }
}
