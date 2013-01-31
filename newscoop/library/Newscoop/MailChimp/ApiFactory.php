<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use ArrayAccess;
use Rezzza\MailChimp\MCAPI;

/**
 */
class ApiFactory
{
    /**
     * @var string
     */
    private $apikey;

    /**
     * @param MCAPI $api
     * @param array $config
     */
    public function __construct(ArrayAccess $config)
    {
        $this->apikey = $config['mailchimp_apikey'];
    }

    /**
     * Create MailChimp API instance
     *
     * @return Rezzza\MailChimp\MCAPI
     */
    public function createApi()
    {
        return new MCAPI($this->apikey, true);
    }
}
