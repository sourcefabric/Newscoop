<?php
// // TODO: Maybe move this to a seperate plugin
// /**
//  * @package Newscoop
//  * @copyright 2012 Sourcefabric o.p.s.
//  * @license http://www.gnu.org/licenses/gpl-3.0.txt
//  */

// namespace Newscoop\Twitter;

// use Newscoop\Search\ServiceInterface;
// use Newscoop\Search\DocumentInterface;
// use Newscoop\Search\RepositoryInterface;

// /**
//  * Search Service
//  */
// class SearchService implements ServiceInterface, RepositoryInterface
// {
//     /**
//      * @var Zend_Http_Client
//      */
//     private $twitterClient;

//     /**
//      * @var Zend_Http_Client
//      */
//     private $solrClient;

//     /**
//      * @var Newscoop\Twitter\AuthService
//      */
//     private $authService;

//     /**
//      * @var array
//      */
//     private $config = array(
//         'id' => '',
//     );

//     /**
//      * @var array
//      */
//     private $deleted = array();

//     // TODO: Replace with symfony http client
//     /**
//      * @var Zend_Http_Client $twitterClient
//      * @var Zend_Http_Client $solrClient
//      * @var array $config
//      */
//     public function __construct(\Zend_Http_Client $twitterClient, \Zend_Http_Client $solrClient, AuthService $authService, array $config)
//     {
//         $this->twitterClient = $twitterClient;
//         $this->solrClient = $solrClient;
//         $this->authService = $authService;
//         $this->config = array_merge($this->config, $config);
//     }

//     /**
//      * Return type for this search service
//      *
//      * @return string identifier
//      */
//     public function getType()
//     {
//         return 'twitter';
//     }

//     /**
//      * Test if tweet is indexed
//      *
//      * @param array $tweet
//      * @return bool
//      */
//     public function isIndexed($tweet)
//     {
//         return in_array($tweet['id_str'], $this->deleted);
//     }

//     /**
//      * Test if tweet can be indexed
//      *
//      * @param array $tweet
//      * @return bool
//      */
//     public function isIndexable($tweet)
//     {
//         return !in_array($tweet['id_str'], $this->deleted);
//     }

//     /**
//      * Get document representation for tweet
//      *
//      * @param array $tweet
//      * @return array
//      */
//     public function getDocument($tweet)
//     {
//         return array(
//             'id' => $this->getDocumentId($tweet),
//             'type' => 'tweet',
//             'tweet_id' => $tweet['id_str'],
//             'published' => gmdate(self::DATE_FORMAT, strtotime($tweet['created_at'])),
//             'tweet' => $tweet['text'],
//             'tweet_user_name' => $tweet['user']['name'],
//             'tweet_user_screen_name' => $tweet['user']['screen_name'],
//             'tweet_user_profile_image_url' => $tweet['user']['profile_image_url'],
//         );
//     }

//     /**
//      * Get document id
//      *
//      * @param array $tweet
//      * @return string
//      */
//     public function getDocumentId($tweet)
//     {
//         return sprintf('%s-%d', $this->getType(), $tweet['id_str']);
//     }

//     /**
//      * Get tweets to be indexed
//      *
//      * @return array
//      */
//     public function getBatch()
//     {
//         $indexed = $this->getIndexedTweets();
//         $this->twitterClient->setParameterGet(array_filter(array(
//             'screen_name' => $this->config['id'],
//             'since_id' => empty($indexed) ? '' : array_pop($indexed), // must remove last as since id won't return it
//         )));

//         $this->twitterClient->setHeaders('Authorization', $this->authService->getToken());

//         $response = $this->twitterClient->request();
//         if (!$response->isSuccessful()) {
//             return array();
//         }

//         $batch = json_decode($response->getBody(), true);
//         $this->deleted = array_diff($indexed, array_map(function($tweet) {
//             return $tweet['id_str'];
//         }, $batch));

//         foreach ($this->deleted as $id) {
//             $batch[] = array('id_str' => $id);
//         }

//         return $batch;
//     }

//     /**
//      * Get indexed tweets
//      *
//      * @param int $rows
//      * @return array
//      */
//     private function getIndexedTweets($rows = 20)
//     {
//         $this->solrClient->setParameterGet(array(
//             'wt' => 'json',
//             'q' => '*:*',
//             'fq' => 'type:tweet',
//             'fl' => 'tweet_id',
//             'sort' => 'published desc',
//             'rows' => (int) $rows,
//         ));

//         $response = $this->solrClient->request();
//         if (!$response->isSuccessful()) {
//             return;
//         }

//         $responseArray = json_decode($response->getBody(), true);
//         return array_map(function($doc) {
//             return $doc['tweet_id'];
//         }, $responseArray['response']['docs']);
//     }

//     /**
//      * Set indexed now
//      *
//      * nop for twitter
//      *
//      * @param array $tweets
//      * @return void
//      */
//     public function setIndexedNow(array $tweets)
//     {
//         return;
//     }

//     /**
//      * Set indexed null
//      *
//      * nop for twitter
//      *
//      * @return void
//      */
//     public function setIndexedNull()
//     {
//         return;
//     }
// }
