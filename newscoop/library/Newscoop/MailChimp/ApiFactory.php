<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use Rezzza\MailChimp\MCAPI;
use Newscoop\NewscoopBundle\Services\SystemPreferencesService;

/**
 */
class ApiFactory
{
    /**
     * @var string
     */
    private $apikey;

    /**
     * @param SystemPreferencesService $service
     */
    public function __construct(SystemPreferencesService $service)
    {
        $this->apikey = $service->get('mailchimp_apikey');
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
