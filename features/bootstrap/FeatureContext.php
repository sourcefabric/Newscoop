<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Buzz\Message\Form\FormUpload;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $browser;

    private $fields = array();

    private $parameters;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext(
            'api',
            new Behat\CommonContexts\WebApiContext($parameters['base_url'], new \Buzz\Browser(new \Buzz\Client\FileGetContents()))
        );
        $this->browser = $this->getMainContext()->getSubcontext('api')->getBrowser();

        $url = str_replace('api/', '', $parameters['base_url']).'oauth/v2/token?client_id=1_svdg45ew371vtsdgd29fgvwe5v&grant_type=client_credentials&client_secret=h48fgsmv0due4nexjsy40jdf3sswwr';
        $this->browser->call($url, 'GET', array());
        $token = json_decode($this->browser->getLastResponse()->getContent(), true);

        $this->getMainContext()->getSubcontext('api')->setPlaceholder('<base_url>', $parameters['base_url']);
        $this->browser->addListener(new PublicationListener(array(
            'publication' => $parameters['publication'],
            'access_token' => $token['access_token']
        )));

        $this->parameters = $parameters;
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
     * @Given /^response should have keys "([^"]*)"$/
     */
    public function responseShouldHaveKeys($keys)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $keys = explode(', ', $keys);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $response)) {
                throw new \Exception('key "'.$key.'" don\'t exist');
            }
        }

        return true;
    }

    /**
     * @Given /^response should have keys "([^"]*)" under "([^"]*)"$/
     */
    public function responseShouldHaveKeysUnder($keys, $mainKey)
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

    /**
     * @Given /^response should have item with keys "([^"]*)"$/
     */
    public function responseShouldHaveItemWithKeys($keys)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $keys = explode(', ', $keys);

        $this->responseShouldHaveWithElements('items');

        $haveAllKeys = false;
        foreach ($response['items'] as $item) {
            $itemKeys = array_keys($item);
            foreach ($keys as $key) {
                if (!in_array($key, $itemKeys)) {
                    continue;
                }

                return true;
            }
        }

        throw new \Exception('There is no items with all provided keys');
    }

    /**
     * @Given /^first item from response should have key "([^"]*)" with value (\d+)$/
     */
    public function firstItemFromResponseShouldHaveKeyWithValue($key, $value)
    {

        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $this->responseShouldHaveWithElements('items');

        if ($response['items'][0][$key] == $value) {
            return true;
        }

        throw new \Exception($key.' don\'t have value '.$value);
    }

    /**
     * @Then /^response should have key "([^"]*)" with value "([^"]*)"$/
     */
    public function responseShouldHaveKeyWithValue($key, $value)
    {

        $response = json_decode($this->browser->getLastResponse()->getContent(), true);

        if ($response[$key] == $value) {
            return true;
        }

        throw new \Exception($key.' don\'t have value '.$value);
    }

    /**
     * @Given /^i should have only "([^"]*)" items$/
     */
    public function iShouldHaveOnlyItems($number)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $this->responseShouldHaveWithElements('items');

        if (count($response['items']) == $number) {
            return true;
        }

        throw new \Exception('Items number is not equal '.$number);
    }

    /**
     * @Given /^response should have item with only "([^"]*)" keys$/
     */
    public function responseShouldHaveItemWithOnlyKeys($keys)
    {
        $response = json_decode($this->browser->getLastResponse()->getContent(), true);
        $keys = explode(', ', $keys);

        $this->responseShouldHaveWithElements('items');

        if (count(array_keys($response['items'][0])) == count($keys)) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $response['items'][0])) {
                    throw new \Exception('Key '.$key.' don\'t exist');
                }

                return true;
            }
        }

        throw new \Exception('Response is wrong');
    }

    /**
     * @Given /^that i want to create new file \'([^\']*)\' with name \'([^\']*)\'$/
     */
    public function thatIWantToSendFileWithName($fileName, $name)
    {
        $upload = new FormUpload(__DIR__.'/assets/'.$fileName);
        $upload->setName($name);
        $this->fields[$name] = $upload;
    }

    /**
     * @Given /^that i want to create "([^"]*)" with name "([^"]*)" and content "([^"]*)" with type "([^"]*)"$/
     */
    public function thatIWantToCreateWithNameAndContentWithType($form, $name, $content, $contentType)
    {
        if ($contentType == 'file') {
            $fileName = $content;
            $content = new FormUpload(__DIR__.'/assets/'.$fileName);
            $content->setName($fileName);
        }

        if (!array_key_exists($form, $this->fields)) {
            $this->fields[$form] = array();
        }

        $this->fields[$form][$name] = $content;
    }

    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^I send a "([^"]*)" request to "([^"]+)" with custom form data$/
     */
    public function iSendARequestToWithCustomFormData($method, $url)
    {
        $webApiContext = $this->getMainContext()->getSubcontext('api');

        if ($url == "last resource") {
            if ($this->browser->getLastResponse()->getHeader('X-Location') !== null) {
                $url = $this->browser->getLastResponse()->getHeader('X-Location');
            } else {
                $url = $this->browser->getLastRequest()->getUrl();
            }
        } else {
            $url = $this->parameters['base_url'].ltrim($webApiContext->replacePlaceHolder($url), '/');
        }

        $this->browser->submit($url, $this->fields, $method);
        $this->fields = array();

        $request  = $this->browser->getLastRequest();
        $response = $this->browser->getLastResponse();

        $this->printDebug(sprintf("%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUrl(),
            $response->getStatusCode(),
            $response->getContent()
        ));
    }

    /**
     * @Given /^response should have header "([^"]*)"$/
     */
    public function responseShouldHaveHeader($name)
    {
        $headers = $this->browser->getLastResponse()->getHeaders();
        if ($this->browser->getLastResponse()->getHeader($name) !== null) {
            return true;
        }

        throw new \Exception('header "'.$name.'" don\'t exist');
    }

    /**
     * @When /^I send a "([^"]*)" request to last resource$/
     */
    public function iSendARequestToLastResource($method)
    {
        $url = $this->browser->getLastRequest()->getUrl();
        if (array_key_exists('X-Location', $this->browser->getLastResponse()->getHeaders())) {
            $url = $this->browser->getLastResponse()->getHeader('X-Location');
        }

        $this->browser->call($url, $method);

        $request  = $this->browser->getLastRequest();
        $response = $this->browser->getLastResponse();

        $this->printDebug(sprintf("%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUrl(),
            $response->getStatusCode(),
            $response->getContent()
        ));
    }
}
