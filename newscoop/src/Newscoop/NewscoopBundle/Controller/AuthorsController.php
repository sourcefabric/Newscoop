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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authors controller.
 */
class AuthorsController extends Controller
{
    /**
     * @Route("/admin/authors/get", options={"expose"=true})
     */
    public function getAuthorsAction(Request $request)
    {
        $authorService = $this->get('author');

        $limit = $request->query->get('limit', 0);
        $term = $request->query->get('term', null);
        $alsoUsers = $request->query->get('users', false);

        if ($alsoUsers == true) {
            $authors = $authorService->getAuthorsAndUsers($term, (int) $limit);
        } else {
            $authors = $authorService->getAuthors($term, (int) $limit);
        }

        return new JsonResponse($authors);
    }
}
