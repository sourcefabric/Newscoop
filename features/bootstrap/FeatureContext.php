<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $browser;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('api',
            new Behat\CommonContexts\WebApiContext($parameters['base_url'], new \Buzz\Browser(new \Buzz\Client\Curl()))
        );

        $this->browser = $this->getMainContext()->getSubcontext('api')->getBrowser();
    }

    /**
     * @Given /^response should contain "code": (\d+)$/
     */
    public function responseShouldContainCode($code)
    {
        assertNotRegExp('/'.preg_quote('"code": '.$code).'/', $this->browser->getLastResponse()->getContent());
    }

    /**
     * @Given /^response should have "([^"]*)" with elements$/
     */
    public function responseShouldHaveWithElements($key)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);

        if (!array_key_exists($key, $response)) {
            throw new \Exception('key "'.$key.'" don\'t exist');
        }

        if (!count($response[$key]) > 0) {
            throw new \Exception('response["'.$key.'"] don\'t have elements');
        }

        return true;
    }

    /**
     * @Given /^response shoud have keys "([^"]*)" under "([^"]*)"$/
     */
    public function responseShoudHaveKeysUnder($keys, $mainKey)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $keys = explode(', ', $keys);

        $this->responseShouldHaveWithElements($mainKey);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $response[$mainKey])) {
                throw new \Exception('key "'.$key.'" don\'t exist');
            }
        }

        return true;
    }

    /**
     * @Given /^response should have "([^"]*)" with elements under "([^"]*)"$/
     */
    public function responseShouldHaveWithElementsUnder($keys, $mainKey)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);

        $this->responseShouldHaveWithElements($mainKey);

        if (!array_key_exists($key, $response[$mainKey])) {
            throw new \Exception('key "'.$key.'" don\'t exist');
        }

        if (!count($response[$mainKey][$key]) > 0) {
            throw new \Exception('response["'.$key.'"] don\'t have elements');
        }

        return true;
    }

}
