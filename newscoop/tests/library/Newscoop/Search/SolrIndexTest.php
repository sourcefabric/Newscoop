<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use DateTime;
use Newscoop\View\ArticleView;

/**
 */
class SolrIndexTest extends \TestCase
{
    const SERVER = 'localhost:1234/solr';
    const UPDATE_URI = '{core}/update';
    const QUERY_URI = '{core}/select{?q,fq,sort,start,rows,fl,wt,df,defType,qf}';

    const DELETE_XML = <<<EOT
<?xml version="1.0"?>
<update><delete><id>123</id></delete></update>

EOT;

    const ADD_XML = <<<EOT
<?xml version="1.0"?>
<update><add><doc><field name="language">en</field></doc><doc><field name="language">en</field></doc></add></update>

EOT;

    /** @var Newscoop\Search\SolrIndex */
    protected $index;

    public function setUp()
    {
        $this->clientFactory = $this->getMockBuilder('Newscoop\Http\ClientFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->index = new SolrIndex($this->clientFactory, array(
            'solr_server' => self::SERVER,
        ));

        $this->client = $this->getMockBuilder('Newscoop\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientFactory->expects($this->any())
            ->method('createClient')
            ->with(self::SERVER)
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
                $this->equalTo(array(self::UPDATE_URI, array('core' => 'en'))),
                $this->equalTo(array('Content-Type' => 'text/xml')),
                $this->equalTo(self::ADD_XML)
            )->will($this->returnValue($this->request));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->response->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $this->index->commit();
    }

    public function testAddFormatDate()
    {
        $now = new DateTime();
        $this->index->add(new ArticleView(array('language' => 'en', 'updated' => $now)));

        $this->client->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->stringContains(sprintf('<field name="updated">%s</field>', gmdate('Y-m-d\TH:i:s\Z', $now->getTimestamp())))
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
                $this->equalTo(self::DELETE_XML)
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

    public function testFind()
    {
        $this->client->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo(array(self::QUERY_URI, array(
                    'core' => 'en',
                    'q' => 'test',
                    'start' => 0,
                    'rows' => 10,
                    'fl' => 'number',
                    'df' => 'title',
                    'wt' => 'json',
                    'defType' => 'edismax',
                    'qf' => 'title',
                )))
            )->will($this->returnValue($this->request));

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($this->response));

        $this->response->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        $this->response->expects($this->once())
            ->method('getBody')
            ->with($this->equalTo(true))
            ->will($this->returnValue(<<<EOT
{
  "responseHeader":{
    "status":0,
    "QTime":1,
    "params":{
      "fl":"number",
      "indent":"false",
      "q":"eco*",
      "qf":"title",
      "wt":"json",
      "defType":"edismax"}},
  "response":{"numFound":1,"start":0,"docs":[
      {
        "number":69}]
}}
EOT
            ));

        $response = $this->index->find(new Query(array(
            'core' => 'en',
            'q' => 'test',
            'qf' => 'title',
        )));

        $this->assertEquals(array((object) array('number' => 69)), $response->docs);
        $this->assertEquals(1, $response->numFound);
    }
}
