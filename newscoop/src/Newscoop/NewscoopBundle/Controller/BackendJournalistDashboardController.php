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

class BackendJournalistDashboardController extends Controller
{
    /**
     * @Route("/admin/dashboard/journalist/", name="newscoop_newscoop_dashboard_journalist")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('em');
        $userService = $this->get('user');
        $author = $userService->getCurrentUser()->getAuthor();

        $commentsPerDay = $em->getRepository('Newscoop\Entity\Comment')
            ->getCommentsForAuthorArticlesPerDay($author)
            ->getArrayResult();
        foreach ($commentsPerDay as $key => $value) {
            $commentsPerDay[$value['date']] = $value;
            unset($commentsPerDay[$key]);
        }

        $articlesPerDay = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForAuthorPerDay($author)
            ->getArrayResult();
        foreach ($articlesPerDay as $key => $value) {
            $articlesPerDay[$value['date']] = $value;
            unset($articlesPerDay[$key]);
        }

        $begin = new \DateTime('NOW');
        $begin->modify('-60 days');
        $end = new \DateTime('NOW');
        $end->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $lineWidgetData = array();
        foreach ( $period as $dt ) {
          $element = array('date' => $dt->format("Y-m-d"));
          if (array_key_exists($dt->format("Y-m-d"), $commentsPerDay)) {
            $element['comments'] = $commentsPerDay[$dt->format("Y-m-d")]['number'];
          } else {
            $element['comments'] = 0;
          }

        if (array_key_exists($dt->format("Y-m-d"), $articlesPerDay)) {
            $element['articles'] = $articlesPerDay[$dt->format("Y-m-d")]['number'];
          } else {
            $element['articles'] = 0;
          }

          $lineWidgetData[] = $element;
        }

        return array(
            'lineWidgetData' => $lineWidgetData
        );
    }

    /**
     * @Route("admin/dashboard/loadAuthorArticles/", options={"expose"=true}, name="newscoop_newscoop_dashboard_author_articles")
     */
    public function loadUsersAction(Request $request)
    {
        $em = $this->get('em');
        $userService = $this->get('user');
        $cacheService = $this->get('newscoop.cache');
        $linkService = $this->get('article.link');
        $author = $userService->getCurrentUser()->getAuthor();

        if (!$author) {
            return new JsonResponse(array('error' => 'No Author'));
        }

        $criteria = $this->getCriteria($request);
        $cacheKey = array('author_articles__'.md5(serialize($criteria)), $author->getId());

        if ($cacheService->contains($cacheKey)) {
            $responseArray =  $cacheService->fetch($cacheKey);
        } else {
            $articlesQuery = $em->getRepository('Newscoop\Entity\Article')
                ->getArticlesForAuthor($author->getId(), $criteria);
            $totalCount = $articlesQuery->getHint('knp_paginator.count');
            $dirtyArticles = $articlesQuery->getResult();

            $articles = array();
            foreach ($dirtyArticles as $key => $article) {
                $articles[] = array(
                    'id' => $article->getNumber().'_'.$article->getLanguageId(),
                    'name' => $article->getName(),
                    'published' => $article->getPublished(),
                    'reads' => $article->getReads(),
                    'link' => $linkService->getLink($article)
                );
            }

            $responseArray = array(
                'records' => $articles,
                'queryRecordCount' => count($articles),
                'totalRecordCount'=> $totalCount
            );

            $cacheService->save($cacheKey, $responseArray);
        }

        return new JsonResponse($responseArray);
    }

    private function getCriteria($request)
    {
        $criteria = new \Newscoop\Criteria();

        if ($request->query->has('sorts')) {
            foreach ($request->get('sorts') as $key => $value) {
                $criteria->orderBy[$key] = $value == '-1' ? 'desc' : 'asc';
            }
        }

        if ($request->query->has('queries')) {
            $queries = $request->query->get('queries');

            if (array_key_exists('search', $queries)) {
                $criteria->query = $queries['search'];
            }
        }

        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }

        return $criteria;
    }
}
