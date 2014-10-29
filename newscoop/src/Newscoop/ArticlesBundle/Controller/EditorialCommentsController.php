<?php
/**
 * @package   Newscoop\ArticlesBundle
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric ź.u.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class EditorialCommentsController extends Controller
{
    /**
     * @Route("/admin/editorial-comments", options={"expose"=true})
     */
    public function getAction(Request $request)
    {
        return array();
    }
}
