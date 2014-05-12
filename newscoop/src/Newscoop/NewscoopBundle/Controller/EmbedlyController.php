<?php
/**
 * @package Newscoop\Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;
use Newscoop\Entity\Snippet;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;


// {"snippetTemplate":{"name":"Embed.ly","controller":"Newscoop\\NewscoopBundle\\Controller\\EmbedlyController","fields":{"url":{"name":"URL","type":"string","scope":"frontend","required":"true"},"endpoint":{"name":"Endpoint","type":"string","scope":"frontend","required":"false"},"maxwidth":{"name":"maxwidth","type":"integer","scope":"frontend","required":"false"},"provider_url":{"name":"provider_url","type":"string","scope":"backend","required":"false"},"description":{"name":"description","type":"string","scope":"backend","required":"false"},"title":{"name":"title","type":"string","scope":"backend","required":"false"},"type":{"name":"type","type":"string","scope":"backend","required":"false"},"thumbnail_width":{"name":"thumbnail_width","type":"string","scope":"backend","required":"false"},"height":{"name":"height","type":"integer","scope":"backend","required":"false"},"width":{"name":"width","type":"integer","scope":"backend","required":"false"},"html":{"name":"html","type":"string","scope":"backend","required":"false"},"author_name":{"name":"author_name","type":"string","scope":"backend","required":"false"},"version":{"name":"version","type":"string","scope":"backend","required":"false"},"provider_name":{"name":"provider_name","type":"string","scope":"backend","required":"false"},"thumbnail_url":{"name":"thumbnail_url","type":"string","scope":"backend","required":"false"},"thumbnail_height":{"name":"thumbnail_height","type":"integer","scope":"backend","required":"false"},"author_url":{"name":"author_url","type":"string","scope":"backend","required":"false"}},"templateCode":"<foo>"}}

class EmbedlyController implements SnippetControllerInterface
{
    private $snippet;
    private $endpoints = array('oEmbed', 'Extract', 'Display', 'Preview', 'Objectify');
    public function __construct(Snippet $snippet, $update = false)
    {
        $this->snippet = $snippet;
        $parameters = array();
        $parameters = $this->preProcess($parameters);
        $parameters = $this->Process($parameters);
        $parameters = $this->postProcess($parameters);
    }

    public function getSnippet()
    {
        return $this->snippet;
    }

    public function update($parameters)
    {
    }

    public function preProcess($parameters)
    {
        $parameters['param']['url'] = $this->snippet->getFields()->get('URL')->getData();
        if (is_null($parameters['param']['url'])) {
            throw new Exception('URL cannot be empty');
        }
        $parameters['endpoint'] = $this->snippet->getFields()->get('Endpoint')->getData();
        if (is_null($parameters['endpoint']) || !in_array($parameters['endpoint'], $this->endpoints)) {
            $parameters['endpoint'] = 'oEmbed';
        }
        $parameters['param']['maxwidth'] = $this->snippet->getFields()->get('maxwidth')->getData();
        if ($parameters['param']['maxwidth'] <= 1) {
            $parameters['param']['maxwidth'] = 550;
        }

        return $parameters;
    }

    public function Process($parameters)
    {
        // set the URL parameters
        $content = '';
        $count = count($parameters['param']);
        foreach($parameters['param'] as $paramName=>$param) {
            $content .= $paramName.'='.rawurlencode($param);
            if (--$count > 0) {
                $content .= '&';
            }
        }

        $request = new \Buzz\Message\Request('GET', '/1/oembed?'.$content, 'http://api.embed.ly'); 
        $response = new \Buzz\Message\Response();

        $client = new \Buzz\Client\FileGetContents();
        $client->send($request, $response);
        if ($response->getStatusCode() == '200') {
            $json = $response->getContent();
            $decoder = new JsonDecode(true);
            $parameters['response'] = $decoder->decode($json, 'json');
        } else {
            throw new Exception('Something went wrong');
        }

        return $parameters;
    }

    public function postProcess($parameters)
    {
        foreach($this->snippet->getFields()->getKeys() as $fieldName) {
            if (array_key_exists($fieldName, $parameters['response'])) {
                $this->snippet->setData($fieldName, $parameters['response'][$fieldName]);
            }
        }

        return $parameters;
    }
}