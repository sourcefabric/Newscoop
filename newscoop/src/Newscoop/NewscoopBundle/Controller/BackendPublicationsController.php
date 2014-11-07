<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\Entity\Publication;
use Newscoop\NewscoopBundle\Form\Type\PublicationType;

class BackendPublicationsController extends Controller
{
    /**
     * @Route("/admin/publications/", name="newscoop_newscoop_publications_index")
     * @Template("NewscoopNewscoopBundle:BackendPublications:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('em');
        $user = $this->container->get('user')->getCurrentUser();

        if (!$user->hasPermission('ManagePub')) {
            throw new AccessDeniedException("You don't have permissions to manage publications");
        }

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->getPubications()
            ->getArrayResult();

        return array(
            'publications' => $publications
        );
    }

    /**
     * @Route("/admin/publications/{id}/edit/", name="newscoop_newscoop_publications_edit")
     * @Template("NewscoopNewscoopBundle:BackendPublications:edit.html.twig")
     */
    public function editAction(Request $request, Publication $publication)
    {
        $form = $this->createForm(new PublicationType(), array(), array('publication_id' => $publication->getId()));

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                ladybug_dump_die($form);
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    private function processRequest($request, $publication = null) {

    }
}
