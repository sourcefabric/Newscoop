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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Aliases;
use Newscoop\NewscoopBundle\Form\Type\PublicationType;
use Newscoop\NewscoopBundle\Form\Type\RemovePublicationType;

class BackendPublicationsController extends Controller
{
    /**
     * @Route("/admin/publications/", name="newscoop_newscoop_publications_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('em');

        $user = $this->container->get('user')->getCurrentUser();
        $translator = $this->container->get('translator');
        if (!$user->hasPermission('ManagePub')) {
            throw new AccessDeniedException($translator->trans("You do not have the right to change publication information.", array(), 'pub'));
        }

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->getPublications()
            ->getArrayResult();

        return $this->render(
            'NewscoopNewscoopBundle:BackendPublications:index.html.twig',
            array(
                'publications' => $publications
            )
        );
    }

    /**
     * @Route("/admin/publications/{id}/edit/", name="newscoop_newscoop_publications_edit")
     */
    public function editAction(Request $request, Publication $publication)
    {
        $user = $this->container->get('user')->getCurrentUser();
        $translator = $this->container->get('translator');
        if (!$user->hasPermission('ManagePub')) {
            throw new AccessDeniedException($translator->trans("You do not have the right to change publication information.", array(), 'pub'));
        }

        $form = $this->createForm(new PublicationType(), $publication, array('publication_id' => $publication->getId()));

        if ($request->getMethod() === 'POST') {
            $form = $this->processRequest($request, $form, $publication);

            if ($form instanceof RedirectResponse) {
                return $form;
            }
        }

        return $this->render(
            'NewscoopNewscoopBundle:BackendPublications:edit.html.twig',
            array(
                'form' => $form->createView(),
                'pageTitle' => $translator->trans('publications.title.edit', array(), 'pub'),
                'publication' => $publication
            )
        );
    }

    /**
     * @Route("/admin/publications/add/", name="newscoop_newscoop_publications_add")
     */
    public function createAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        $translator = $this->container->get('translator');
        if (!$user->hasPermission('ManagePub')) {
            throw new AccessDeniedException($translator->trans("You do not have the right to add publications.", array(), 'pub'));
        }

        $form = $this->createForm(new PublicationType(), new Publication());

        if ($request->getMethod() === 'POST') {
            return $this->processRequest($request, $form);
        }

        return $this->render(
            'NewscoopNewscoopBundle:BackendPublications:edit.html.twig',
            array(
                'form' => $form->createView(),
                'pageTitle' => $translator->trans('publications.title.add', array(), 'pub')
            )
        );
    }

    /**
     * @Route("/admin/publications/{id}/remove/", name="newscoop_newscoop_publications_remove")
     */
    public function removeAction(Request $request, Publication $publication)
    {
        $em = $this->container->get('em');
        $user = $this->container->get('user')->getCurrentUser();
        $translator = $this->container->get('translator');
        if (!$user->hasPermission('ManagePub')) {
            throw new AccessDeniedException($translator->trans("You do not have the right to delete publications.", array(), 'pub'));
        }

        $issuesRemaining = $em->getRepository('Newscoop\Entity\Issue')->getIssuesCountForPublication($publication->getId())->getSingleScalarResult();
        $sectionsRemaining = $em->getRepository('Newscoop\Entity\Section')->getSectionsCountForPublication($publication->getId())->getSingleScalarResult();
        $articlesRemaining = $em->getRepository('Newscoop\Entity\Article')->getArticlesCountForPublication($publication->getId())->getSingleScalarResult();

        $form = $this->createForm(new RemovePublicationType(), $publication);

        if ($request->getMethod() === 'POST' && $issuesRemaining == 0 && $sectionsRemaining == 0 && $articlesRemaining == 0) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->remove($publication);
                $em->flush();

                $cacheService = $this->container->get('newscoop.cache');
                $cacheService->clearNamespace('publication');

                $this->get('session')->getFlashBag()->add('success', $translator->trans('publications.publication_removed', array(), 'pub'));

                return new RedirectResponse($this->generateUrl('newscoop_newscoop_publications_index'));
            }
        }

        return $this->render(
            'NewscoopNewscoopBundle:BackendPublications:remove.html.twig',
            array(
                'publication' => $publication,
                'issuesRemaining' => $issuesRemaining,
                'sectionsRemaining' => $sectionsRemaining,
                'articlesRemaining' => $articlesRemaining,
                'form' => $form->createView()
            )
        );
    }

    private function processRequest($request, $form, $publication = null)
    {
        $em = $this->container->get('em');

        $form->handleRequest($request);
        if ($form->isValid()) {
            if(!$publication) {
                $attributes = $form->getData();
                $alias = new Aliases();
                $alias->setName($attributes->getDefaultAlias());
                $em->persist($alias);
                $em->flush();
                $attributes->setDefaultAlias($alias);
                $em->persist($attributes);
                $alias->setPublication($attributes);
            }
            $em->flush();

            $cacheService = $this->container->get('newscoop.cache');
            $cacheService->clearNamespace('publication');

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('publications.publication_saved', array(), 'pub'));

            return new RedirectResponse($this->generateUrl('newscoop_newscoop_publications_index'));
        }

        return $form;
    }
}
