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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CommentsController extends FOSRestController
{
    /**
     * @Route("/articles/{id}/{language}/comments.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getCommentsAction($id, $language)
    {
        return array('test' => 'test23');
    }
}