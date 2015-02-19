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

/**
 * Playlists controller.
 */
class PlaylistsController extends Controller
{
    /**
     * @Route("/admin/playlists/")
     */
    public function indexAction()
    {
        $preferencesService = $this->get('preferences');
        $em = $this->get('em');
        $clientName = 'newscoop_'.$preferencesService->SiteSecretKey;
        $client = $em->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findOneByName($clientName);

        return $this->render('NewscoopNewscoopBundle:Playlists:index.html.twig', array(
            'clientId' => $client ? $client->getPublicId() : '',
        ));
    }
}
