<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use Newscoop\GimmeBundle\Util\ExceptionWrapper;
use FOS\RestBundle\Controller\ExceptionController as FOSExceptionController;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends FOSExceptionController
{

    protected function createExceptionWrapper(array $parameters)
    {
        return $parameters;
    }

    protected function getParameters(ViewHandler $viewHandler, $currentContent, $code, FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $defaultParameters = parent::getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);
        $parameters = array(
            'errors' => array(
                array(
                    'code' => $defaultParameters['status_code'],
                    'message' => $defaultParameters['message'],
                )
            )
        );

        return $parameters;
    }
}