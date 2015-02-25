<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionsController extends FOSRestController
{
    /**
     * Get Sections
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when sections found",
     *         404={
     *           "Returned when sections are not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="publication", "dataType"="integer", "required"=true, "description"="Publication id"},
     *         {"name"="issue", "dataType"="integer", "required"=true, "description"="Issue number"}
     *     }
     * )
     *
     * @Route("/sections.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getSectionsAction(Request $request)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $issueNumber = $em->getRepository('Newscoop\Entity\Issue')
            ->getByPublicationAndNumber(
                $request->query->get('publication', $publication),
                $request->query->get('issue')
            )
            ->getOneOrNullResult();

        if (!$issueNumber & $request->query->has('issue')) {
            throw new NotFoundHttpException('Issue was not found.');
        }

        $sections = $em->getRepository('Newscoop\Entity\Section')
            ->getSections(
                $request->query->get('publication', $publication),
                $issueNumber
            )->getResult();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $sections = $paginator->paginate($sections, array(
            'distinct' => false,
        ));

        return $sections;
    }

    /**
     * Get section articles
     *
     * Returns array with articles under "items" key and requested section "id" and "title"
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the section articles are not found",
     *         }
     *     }
     * )
     *
     * @Route("/sections/{number}/{language}/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getSectionsArticlesAction(Request $request, $number, $language)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('number' => $number, 'language' => $language));

        $language = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

        $section = $em->getRepository('Newscoop\Entity\Section')
            ->findOneBy(array(
                'number' => $number,
                'language' => $language,
                'publication' => $publication,
            ));

        if (!$section) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForSection($publication, $number);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false,
        ));

        $allItems = array_merge(array(
            'id' => $section->getId(),
            'title' => $section->getName(),
        ), $articles);

        return $allItems;
    }
}
