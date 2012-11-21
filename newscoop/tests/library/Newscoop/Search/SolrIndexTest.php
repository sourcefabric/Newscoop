<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Newscoop\View\ArticleView;

/**
 */
class SolrIndexTest extends \TestCase
{
    const UPDATE_URL = 'localhost/{core}/update';

    /** @var Newscoop\Search\SolrIndex */
    protected $index;

    public function setUp()
    {
        $this->clientFactory = $this->getMockBuilder('Newscoop\Http\ClientFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->index = new SolrIndex($this->clientFactory, array(
            'update_url' => self::UPDATE_URL,
        ));

        $this->client = $this->getMockBuilder('Newscoop\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientFactory->expects($this->any())
            ->method('createClient')
            ->will($this->returnValue($this->client));

        $this->response = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Search\SolrIndex', $this->index);
    }

    public function testAddNoCommit()
    {
        $this->index->add(new ArticleView());

        $this->client->expects($this->never())
            ->method('post');
    }

    public function testAdd()
    {
        $doc = new ArticleView(array('language' => 'en'));
        $this->index->add($doc);
        $this->index->add($doc);

        $this->client->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(array(self::UPDATE_URL, array('core' => 'en'))),
                $this->equalTo(array('Content-Type' => 'text/json')),
                $this->equalTo('{"add":{"doc":' . json_encode($doc) . '},"add":{"doc":' . json_encode($doc) . '}}')
            )->will($this->returnValue($this->request));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->response->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $this->index->commit();
    }

    public function testDelete()
    {
        $doc = new ArticleView(array('number' => 123, 'language' => 'en'));
        $this->index->delete($doc);

        $this->client->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->equalTo('{"delete":{"number":123}}')
            )->will($this->returnValue($this->request));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->response->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $this->index->commit();
    }

    /**
     * @expectedException Newscoop\Search\SolrException
     */
    public function testCommitException()
    {
        $this->client->expects($this->once())
            ->method('post')
            ->will($this->returnValue($this->request));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->response->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $this->index->add(new ArticleView(array('language' => 'en')));
        $this->index->commit();
    }
}
