<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Newscoop\Version;

/**
 * Help controller.
 */
class HelpController extends Controller
{
    /**
     * @Route("/admin/application/help/")
     */
    public function indexAction()
    {
        $newscoop = new \CampVersion();
        $preferencesService = $this->get('preferences');
        $entityManager = $this->get('em');
        $defaultClientName = 'newscoop_'.$preferencesService->SiteSecretKey;
        $client = $entityManager->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findOneBy(array(
            'name' => $defaultClientName,
        ));

        return $this->render('NewscoopNewscoopBundle:Help:index.html.twig', array(
            'version' => $newscoop->getVersion(),
            'release' => $newscoop->getRelease(),
            'apiVersion' => Version::API_VERSION,
            'releaseDate' => $newscoop->getReleaseDate(),
            'clientId' => $client->getPublicId(),
        ));
    }
}
