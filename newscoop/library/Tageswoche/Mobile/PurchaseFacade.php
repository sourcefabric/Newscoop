<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Mobile;

use Newscoop\Http\ClientFactory;
use Newscoop\Entity\User;

/**
 */
class PurchaseFacade
{
    const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';
    const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

    const STATUS_ERROR = 1;
    const STATUS_SANDBOX = 21007;

    const CONFIG_PASSWORD = 'mobile_shared_secret';

    /**
     * @var Newscoop\Http\ClientFactory
     */
    private $clientFactory;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Newscoop\Http\ClientFactory $clientFactory
     * @param array $config
     */
    public function __construct(ClientFactory $clientFactory, array $config)
    {
        $this->clientFactory = $clientFactory;
        $this->config = $config;
    }

    /**
     * Validate receipt
     *
     * @param string $receipt
     * @return array
     */
    public function validate($receipt)
    {
        $data = $this->getResponse(self::PRODUCTION_URL, $receipt);
        if ($data->status === self::STATUS_SANDBOX) {
            $data = $this->getResponse(self::SANDBOX_URL, $receipt);
        }

        $return = array(
            'receipt_valid' => $data->status === 0,
            'product_id' => $data->status === 0 ? $data->receipt->product_id : null,
        );

        if (!empty($data->latest_receipt_info)) {
            $return['expires_date'] = gmdate('Y-m-d H:i:s', round($data->latest_receipt_info->expires_date / 1000.0));
        }

        return $return;
    }

    /**
     * Test if receipt is valid
     *
     * @param string $receipt
     * @return bool
     */
    public function isValid($receipt)
    {
        $status = $this->validate($receipt);
        return $status['receipt_valid'];
    }

    /**
     * Get response for validate request
     *
     * @param string $url
     * @param string $receipt
     * @return object
     */
    private function getResponse($url, $receipt)
    {
        $client = $this->clientFactory->getClient();
        $response = $client->post(
            $url,
            null,
            json_encode(
                array(
                    'receipt-data' => $receipt,
                    'password' => $this->getPassword(),
                )
            )
        )->send();

        if (!$response->isSuccessful()) {
            return (object) array(
                'status' => self::STATUS_ERROR,
            );
        }

        return json_decode($response->getBody(true));
    }

    /**
     * Get mobile app password
     *
     * @return string
     */
    private function getPassword()
    {
        return !empty($this->config[self::CONFIG_PASSWORD]) ? $this->config[self::CONFIG_PASSWORD] : '';
    }
}

