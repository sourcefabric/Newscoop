<?php
/**
 * @package   Newscoop\NewscoopBundle
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Webcode Redirect Controller
 */
class WebcodeRedirectController extends Controller
{
    /**
     * @Route("/{webcode}", requirements={"webcode" = "^\+[a-z0-9]{5}"})
     */
    public function webcodeRedirectAction(Request $request, $webcode)
    {
        $em = $this->get('em');
        $linkService = $this->get('article.link');
        $article = $em->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('a')
            ->where('a.webcode = :webcode')
            ->setParameter('webcode', str_replace('+', '', $webcode))
            ->getQuery()
            ->getOneOrNullResult();

        if (!is_null($article)) {
            $link = $linkService->getLinkCanonical($article);

            return $this->redirect($link, 301);
        }

        return $this->redirect('/');
    }
}
