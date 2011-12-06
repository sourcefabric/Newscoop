<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Reuters feed
 * @Document(collection="news_feed")
 */
class ReutersFeed extends Feed
{
    const TOKEN_TTL = 'PT12H';

    const DATE_FORMAT = 'Y.m.d.H.i';

    const MAX_AGE = '5m';

    const STATUS_SUCCESS = 10;
    const STATUS_PARTIAL_SUCCESS = 20;

    /**
     * @var Zend_Rest_Client
     */
    protected $client;

    /**
     * @String
     * @var string
     */
    protected $token;

    /**
     * @Date
     * @var DateTime
     */
    protected $tokenUpdated;

    /**
     * @param array $configuration
     * @param Zend_Rest_Client $client
     */
    public function __construct(array $configuration)
    {
        $this->setConfiguration($configuration);
    }

    /**
     * Update feed
     *
     * @return void
     */
    public function update(\Doctrine\Common\Persistence\ObjectManager $om)
    {
        foreach ($this->getChannels() as $channel) {
            if ($this->updated !== null && $this->updated->getTimestamp() > $channel->lastUpdate->getTimestamp()) {
                continue;
            }

            foreach ($this->getChannelItems($channel) as $channelItem) {
                if (empty($channelItem->guid)) {
                    var_dump('channel', $channelItem);
                    exit;
                }
                $item = $this->getItem($channelItem->guid); // get the latest revision
                if ($item !== null) {
                    $item->setFeed($this);
                    $om->persist($item);
                }
            }
        }

        $this->updated = new \DateTime();
        $om->flush();
    }

    /**
     * Get list of subscribed channels
     *
     * @return array
     */
    public function getChannels()
    {
        $response = $this->getClient()->restGet('/rmd/rest/xml/channels', array(
            'token' => $this->getToken(),
        ));

        $xml = $this->parseResponse($response);

        $channels = array();
        foreach ($xml->channelInformation as $channelInformation) {
            $channels[] = (object) array(
                'description' => (string) $channelInformation->description,
                'alias' => (string) $channelInformation->alias,
                'lastUpdate' => new \DateTime((string) $channelInformation->lastUpdate),
                'category' => (string) $channelInformation['description'],
            );
        }

        return $channels;
    }

    /**
     * Get list of items in channel
     *
     * @param object $channel
     * @return array
     */
    private function getChannelItems($channel)
    {
        $params = array(
            'token' => $this->getToken(),
            'channel' => $channel->alias,
            'fieldsRef' => 'id',
        );

        if ($this->updated !== null) {
            $params['dateRange'] = $this->updated->format(self::DATE_FORMAT);
        } else {
            $params['maxAge'] = self::MAX_AGE;
        }

        $response = $this->getClient()->restGet('/rmd/rest/xml/items', $params);
        $xml = $this->parseResponse($response);

        $items = array();
        foreach ($xml->result as $result) {
            $items[] = (object) array(
                'id' => (string) $result->id,
                'guid' => (string) $result->guid,
                'version' => (string) $result->version,
            );
        }

        return $items;
    }

    /**
     * Get item
     *
     * @param string $id
     * @return Newscoop\News\NewsItem
     */
    private function getItem($id)
    {
        $response = $this->getClient()->restGet('/rmd/rest/xml/item', array(
            'token' => $this->getToken(),
            'id' => $id,
        ));

        $xml = $this->parseResponse($response);
        if (!empty($xml->itemSet->newsItem)) {
            return new NewsItem($xml->itemSet->newsItem);
        } else if (!empty($xml->packageItem)) {
            return new PackageItem($xml->packageItem);
        } else if (!empty($xml->itemSet->packageItem)) {
            return new PackageItem($xml->itemSet->packageItem);
        } else {
            var_dump('not implemented', $xml->asXML());
            exit;
            throw new \InvalidArgumentException("Not implemented");
        }

        return;
    }

    /**
     * Get auth token
     *
     * @return string
     */
    private function getToken()
    {
        if ($this->token === null
            || ($this->tokenUpdated !== null && $this->tokenUpdated->add(new \DateInterval(self::TOKEN_TTL))->getTimestamp() > time())) {
            $client = $this->getClient();
            $client->setUri('https://commerce.reuters.com/');
            $response = $client->restGet('/rmd/rest/xml/login', array(
                'username' => $this->configuration['reuters']['username'],
                'password' => $this->configuration['reuters']['password'],
            ));

            if (!$response->isSuccessful()) {
                throw new \RuntimeException("Can't get auth token");
            }

            $this->token = (string) simplexml_load_string($response->getBody());
            $this->tokenUpdated = new \DateTime();
        }

        return $this->token;
    }

    /**
     * Set client
     *
     * @param Zend_Rest_Client $client
     * @return void
     */
    public function setClient(\Zend_Rest_Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get client
     *
     * @return Zend_Rest_Client
     */
    private function getClient()
    {
        if ($this->client === null) {
            return new \Zend_Rest_Client('http://rmb.reuters.com/');
        }

        return $this->client;
    }

    /**
     * Throw an exception if error occured
     *
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    private function parseResponse(\Zend_Http_Response $response)
    {
        if (!$response->isSuccessful()) {
            throw new \RuntimeException("Response not successful.");
        }

        $xml = simplexml_load_string($response->getBody());
        if ($xml->getName() !== 'newsMessage' && !in_array((int) $xml->status['code'], array(self::STATUS_SUCCESS, self::STATUS_PARTIAL_SUCCESS))) {
            throw new \RuntimeException((string) $xml->status->error);
        }

        return $xml;
    }
}
