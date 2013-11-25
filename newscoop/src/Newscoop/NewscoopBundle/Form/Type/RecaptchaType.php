<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use EWZ\Bundle\RecaptchaBundle\Form\Type\RecaptchaType as BaseRecaptcha;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaType extends BaseRecaptcha
{
    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {   
        $preferensService = $container->get('system_preferences_service');
        $this->publicKey = $preferensService->get('RecaptchaPublicKey');
        $this->secure = $preferensService->get('RecaptchaSecure') == 'Y' ? true : false;
        $this->enabled = $container->getParameter('ewz_recaptcha.enabled');
    }
}