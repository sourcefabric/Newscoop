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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DefaultController extends FOSRestController
{
	/**
     * @Route("/.{_format}", defaults={"_format"="json"})
     * @View()
     */
    public function indexAction()
    {
    	$data = array(
    		'name' => 'Articles',
    		'url' => 'http://example.com'
    	);

        return $data;
    }

    /**
     * @Route("/exception.{_format}", defaults={"_format"="json"})
     * @View()
     */
    public function exceptionAction()
    {
    	throw new ResourceNotFoundException();
    }
}
