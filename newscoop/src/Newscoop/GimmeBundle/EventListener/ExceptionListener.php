<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as SymfonyExceptionListener;
use Symfony\Component\HttpKernel\Exception\FlattenException;

/**
 * ExceptionListener.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionListener extends SymfonyExceptionListener
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $handling = true;

        $exception = $event->getException();
        $request = $event->getRequest();

        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        $attributes = array(
            '_controller' => $this->controller,
            'exception'   => FlattenException::create($exception),
            'logger'      => $this->logger instanceof DebugLoggerInterface ? $this->logger : null,
            // keep for BC -- as $format can be an argument of the controller callable
            // see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
            // @deprecated in 2.4, to be removed in 3.0
            'format'      => $request->getRequestFormat(),
        );

        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod($event->getRequest()->getMethod());

        try {
            $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
        } catch (\Exception $e) {
            $this->logException($exception, sprintf('Exception thrown when handling an exception (%s: %s)', get_class($e), $e->getMessage()), false);

            // set handling to false otherwise it wont be able to handle further more
            $handling = false;

            // re-throw the exception from within HttpKernel as this is a catch-all
            return;
        }

        $event->setResponse($response);

        $handling = false;
    }
}
