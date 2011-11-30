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
    const TOKEN_TTL = 43200; // 12h

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
    public function __construct(array $configuration, \Zend_Rest_Client $client)
    {
        $this->setConfiguration($configuration);

        $this->client = $client;
        $this->client->setUri('http://rmb.reuters.com/');
    }

    /**
     * Update feed
     *
     * @return void
     */
    public function update()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Get list of subscribed channels
     *
     * @return array
     */
    public function getChannels()
    {
        $response = $this->client->restGet('/rmd/rest/xml/channels', array(
            'token' => $this->getToken(),
        ));

        $xml = simplexml_load_string($response->getBody());

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
    public function getChannelItems($channel)
    {
        $response = $this->client->restGet('/rmd/rest/xml/items', array(
            'token' => $this->getToken(),
            'channel' => $channel->alias,
        ));

        $xml = simplexml_load_string($response->getBody());

        $items = array();
        foreach ($xml->result as $result) {
            $items[] = (object) array(
                'id' => (string) $result->id,
                'guid' => (string) $result->guid,
                'version' => (string) $result->version,
                'dateCreated' => new \DateTime((string) $result->dateCreated),
                'slug' => (string) $result->slug,
                'author' => (string) $result->author,
                'source' => (string) $result->source,
                'language' => (string) $result->language,
                'headline' => (string) $result->headline,
                'mediaType' => (string) $result->mediaType,
                'priority' => (int) $result->priority,
                'geography' => (string) $result->geography,
                'previewUrl' => (string) $result->previewUrl,
                'size' => (int) $result->size,
                'dimensions' => (string) $result->dimensions,
                'channel' => (string) $result->channel,
            );
        }

        return $items;
    }

    /**
     * Get item
     *
     * @param string $id
     * @return Newscoop\News\Item
     */
    public function getItem($id)
    {
        $response = $this->client->restGet('/rmd/rest/xml/item', array(
            'token' => $this->getToken(),
            'id' => $id,
        ));

        $xml = simplexml_load_string($response->getBody());
        $item = NewsItem::createFromXml($xml->itemSet->newsItem);
        $item->setFeed($this);
        return $item;
    }

    /**
     * Get auth token
     *
     * @return string
     */
    private function getToken()
    {
        if ($this->token === null) {
            $client = clone $this->client;
            $client->setUri('https://commerce.reuters.com/');
            $response = $client->restGet('/rmd/rest/xml/login', array(
                'username' => $this->configuration['reuters']['username'],
                'password' => $this->configuration['reuters']['password'],
            ));

            $xml = simplexml_load_string($response->getBody());
            $this->token = (string) $xml;
        }

        return $this->token;
    }
}
