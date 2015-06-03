<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Language controller.
 */
class LanguageController extends Controller
{
    /**
     * @Route("/admin/languages/get-all", options={"expose"=true})
     */
    public function getLanguages(Request $request)
    {
        $languages = $this->get('em')
            ->getRepository('Newscoop\Entity\Language')
            ->getAllLanguages()
            ->getArrayResult();

        return new JsonResponse(array(
            'languages' => $languages,
        ));
    }
}
