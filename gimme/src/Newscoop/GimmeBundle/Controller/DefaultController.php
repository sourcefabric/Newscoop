<?php
/**
 * @package Newscoop\Gimme
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
    	$data = array(
    		'name' => 'Articles',
    		'url' => 'http://example.com'
    	);

	    return new Response(json_encode($data));
    }
}
