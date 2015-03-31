<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException as SymfonyAuthenticationException;

/**
 * Authentication exception, thrown when user is not authenticated.
 */
class AuthenticationException extends SymfonyAuthenticationException
{
}
