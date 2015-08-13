<?php
/**
 * @author   Demin Yin <deminy@deminy.net>
 * @license  MIT license
 */
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Buzz\Message\Form\FormUpload;

/**
 * Rest context.
 */
class RestContext extends BehatContext
{
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_PATCH  = 'PATCH';
    const METHOD_LINK   = 'LINK';
    const METHOD_UNLINK = 'UNLINK';

    /**
     * @var \Buzz\Browser
     */
    protected $client;

    /**
     * @var string
     */
    protected $requestMethod;

    /**
     * Used for debugging purpose only.
     * @var string
     */
    protected $requestUrl;

    /**
     * Client access_token
     * @var string
     */
    protected $access_token = null;

    /**
     * Data collected from steps
     * @var string
     */
    protected $collectedData = array();

    /**
     * Locations collected from steps
     * @var array
     */
    protected $locations = array();

    /**
     * Headers
     * @var array
     */
    protected $headers = array();

    /**
     * @var \Buzz\Message\Response
     */
    protected $response;

    /**
     * Data decoded from HTTP response.
     * @var mixed
     */
    protected $responseData;

    /**
     * Specifies if the response data should be an associative array or a nested stdClass object hierarchy.
     *
     * @var bool
     */
    protected $associative;

    /**
     * @var boolean
     */
    protected $responseIsJson;

    /**
     * @var \Exception
     */
    protected $responseDecodeException;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters Context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $fileGetContent = new \Buzz\Client\FileGetContents();
        $fileGetContent->setTimeout(15);
        $this->client      = new \Buzz\Browser($fileGetContent);
        $this->associative = (array_key_exists('associative', $parameters) ? $parameters['associative'] : true);
    }

    /**
     * @Given /^that I want to (delete|remove) an? /
     * @return void
     */
    public function thatIWantToDelete()
    {
        $this->requestMethod = self::METHOD_DELETE;
    }

    /**
     * @Given /^that I want to ((find|look for) an?|check) /
     * @return void
     */
    public function thatIWantToFind()
    {
        $this->requestMethod = self::METHOD_GET;
    }

    /**
     * @Given /^that I want to (add|create|make) an? (new )?/
     * @return void
     */
    public function thatIWantToMakeANew()
    {
        $this->requestMethod = self::METHOD_POST;
    }

    /**
     * @Given /^that I want to (change|update) (an?|that) /
     * @return void
     */
    public function thatIWantToUpdate()
    {
        $this->requestMethod = self::METHOD_PATCH;
    }

    /**
     * @Given /^that I want to link (an?|that) /
     * @return void
     */
    public function thatIWantToLink()
    {
        $this->requestMethod = self::METHOD_LINK;
    }

    /**
     * @Given /^that I want to unlink (an?|that) /
     * @return void
     */
    public function thatIWantToUnlink()
    {
        $this->requestMethod = self::METHOD_UNLINK;
    }

    /**
     * @Given /^I\'m autheticated with client "([^"]*)" and secret "([^"]*)"$/
     * @param  string     $clientId
     * @return void
     * @throws \Exception
     */
    public function authenitcatedWith($client, $secret)
    {
        $tokenUrl = '/v2/token?client_id='.$client.'&grant_type=client_credentials&client_secret='.$secret;
        $this->client->call($this->getMainContext()->getParameter('oauth_url').$tokenUrl, 'GET');
        $this->response = $this->client->getLastResponse();

        $this->processResponse();

        $this->access_token = $this->responseData['access_token'];
    }

    /**
     * @Given /^I\'m logged in as "([^"]*)" with "([^"]*)" with client "([^"]*)" and secret "([^"]*)"$/
     * @param  string     $username
     * @param  string     $password
     * @param  string     $client
     * @return void
     * @throws \Exception
     */
    public function authenitcatedAsWithClient($username, $password, $client, $secret)
    {
        $tokenUrl = '/v2/token?client_id='.$client.'&grant_type=password&username='.$username.'&password='.$password.'&client_secret='.$secret;
        $this->client->call($this->getMainContext()->getParameter('oauth_url').$tokenUrl, 'GET');
        $this->response = $this->client->getLastResponse();

        $this->processResponse();

        $this->access_token = $this->responseData['access_token'];
    }

    /**
     * @Given /^that i have folowing "([^"]*)" data:$/
     * @param  string     $clientId
     * @return void
     * @throws \Exception
     */
    public function collectData($dataKey, \Behat\Gherkin\Node\TableNode $data)
    {
        foreach ($data->getRows() as $key => $value) {
            $this->collectedData[$dataKey][$value[0]] = $value[1];
        }
    }

    /**
     * @Given /^that i have "([^"]*)" header with "([^"]*)" value$/
     * @param  string     $clientId
     * @return void
     * @throws \Exception
     */
    public function collectHeaders($key, $value)
    {
        if (strpos($value, '$$') !== false) {
            $start = '$$';
            $end = '$$';

            $startpos = strpos($value, $start) + strlen($start);
            if (strpos($value, $start) !== false) {
                $endpos = strpos($value, $end, $startpos);
                if (strpos($value, $end, $startpos) !== false) {
                    $link = substr($value, $startpos, $endpos - $startpos);
                }
            }

            $value = str_replace('$$', '', str_replace('$$', '', str_replace("$$".$link."$$", $this->locations[$link], $value)));
        }

        $this->headers[$key] = $value;
    }

    /**
     * @Given /^that i have fake "([^"]*)" data:$/
     * @param string                        $dataKey
     * @param \Behat\Gherkin\Node\TableNode $data
     *
     * @return boolean
     */
    public function collectFakeData($dataKey, \Behat\Gherkin\Node\TableNode $data)
    {
        $faker = Faker\Factory::create();

        $this->collectedData[$dataKey] = array();
        foreach ($data->getRows() as $key => $value) {
            if (strpos($value[1], '<<') !== false) {
                $functionName = str_replace('>>', '', str_replace('<<', '', $value[1]));
                $fakeValue = call_user_func_array(array($faker, $functionName), explode(',', $value[2]));
                if ($functionName == 'file' || $functionName == 'image') {
                    $fakeValue = new FormUpload($fakeValue);
                }

                if (strpos($value[0], '[') !== false) {
                    parse_str($value[0].'='.$fakeValue, $temp);
                    $temp = array($dataKey => $temp);
                    $this->collectedData = array_merge_recursive($this->collectedData, $temp);
                    continue;
                }

                $this->collectedData[$dataKey][$value[0]] = $fakeValue;
            } else {
                if (strpos($value[0], '[') !== false) {
                    parse_str($value[0].'='.$fakeValue, $temp);
                    $temp = array($dataKey => $temp);
                    $this->collectedData = array_merge_recursive($this->collectedData, $temp);
                    continue;
                }

                if (strpos($value[1], '(') !== false) {
                    $locationIndex = str_replace(')', '', str_replace('(', '', $value[1]));
                    $locationValue = $this->locations[$locationIndex];
                    parse_str($value[0].'='.$locationValue, $temp);
                    $temp = array($dataKey => $temp);
                    $this->collectedData = array_merge_recursive($this->collectedData, $temp);
                    continue;
                }

                $this->collectedData[$dataKey][$value[0]] = $value[1];
            }
        }

        return true;
    }

    /**
     * @Then /^save new item location as "([^"]*)"$/
     * @param string $location
     *
     * @return boolean
     */
    public function collectLocations($locationIndex)
    {
        $this->response = $this->client->getLastResponse();
        $this->locations[$locationIndex] = $this->response->getHeader('X-Location');
    }

    /**
     * @Then /^save "([^"]+)" field under location "([^"]*)"$/
     * @param  string         $fieldName
     * @param  string         $locationIndex
     * @return Step\Then|void
     * @throws Exception
     */
    public function saveFieldValueUnderLocation($fieldName, $locationIndex)
    {
        $this->response = $this->client->getLastResponse();
        $fieldValue = null;
        if ($this->responseIsJson) {
            if ($this->associative) {
                if (!(is_array($this->responseData)) || !array_key_exists($fieldName, $this->responseData)) {
                    throw new \Exception('Field "'.$fieldName.'" is not set!');
                }

                $fieldValue = $this->responseData[$fieldName];
            } else {
                if (!($this->responseData instanceof stdClass) || !property_exists($this->responseData, $name)) {
                    throw new \Exception('Field "'.$fieldName.'" is not set!');
                }

                $fieldValue = $this->responseData->$fieldName;
            }

            $this->locations[$locationIndex] = $fieldValue;
        } else {
            return new Step\Then('the response is JSON');
        }
    }

    /**
     * @When /^I request "([^"]*)"$/
     * @param  string     $pageUrl
     * @return void
     * @throws \Exception
     */
    public function iRequest($pageUrl)
    {
        $this->responseData = $this->responseDecodeException = null;
        $this->responseIsJson = false;

        if ($this->access_token) {
            $this->headers['Authorization'] = 'Bearer '.$this->access_token;
        }

        $this->client->call(
            $this->processPageUrl($pageUrl),
            strtolower($this->requestMethod),
            $this->headers
        );
        $this->headers = array();
        $this->response = $this->client->getLastResponse();
    }

    /**
     * @When /^I submit "([^"]*)" data to "([^"]*)"$/
     * @param  string     $pageUrl
     * @return void
     * @throws \Exception
     */
    public function iSubmitDataTo($dataKey, $pageUrl)
    {
        $this->responseData = $this->responseDecodeException = null;
        $this->responseIsJson = false;

        if ($this->access_token) {
            $this->headers['Authorization'] = 'Bearer '.$this->access_token;
        }

        $this->client->submit(
            $this->processPageUrl($pageUrl),
            array($dataKey => $this->collectedData[$dataKey]),
            strtolower($this->requestMethod),
            $this->headers
        );

        $this->headers = array();
        $this->response = $this->client->getLastResponse();
    }

    public function processPageUrl($pageUrl)
    {
        if (strpos($pageUrl, '<<') !== false) {
            $urlParts = explode('>>', $pageUrl);
            $locationIndex = str_replace('>>', '', str_replace('<<', '', $urlParts[0]));

            if (strlen($urlParts[1]) > 0){
                return $this->locations[$locationIndex].$urlParts[1];
            }

            return $this->locations[$locationIndex];
        }

        return $this->getMainContext()->getParameter('base_url').$pageUrl;
    }

    /**
     * This public method is also for other context(s) to process REST API call and inject response into this context.
     *
     * @param  \Buzz\Message\Response $response
     * @param  boolean                $asJson   Process the response as JSON or not.
     * @return void
     */
    public function processResponse(\Buzz\Message\Response $response = null, $asJson = true)
    {
        if (!empty($response)) {
            $this->response = $response;
        }

        return $this->processResponseBody($this->response->getContent(), $asJson);
    }

    /**
     * Process response body. This method may also be used by other context(s) to process REST API call and inject
     * response body into this context by using 2nd parameter $asJson.
     *
     * @param  string  $jsonData
     * @param  boolean $asJson
     * @return void
     */
    protected function processResponseBody($jsonData, $asJson = true)
    {
        if ($asJson) {
            try {
                $this->responseData            = $this->decodeJson($jsonData);
                $this->responseIsJson          = true;
                $this->responseDecodeException = null;
            } catch (\Exception $e) {
                $this->responseData            = $jsonData;
                $this->responseIsJson          = false;
                $this->responseDecodeException = $e;
            }
        } else {
            $this->responseData            = $jsonData;
            $this->responseIsJson          = false;
            $this->responseDecodeException = null;
        }
    }

    /**
     * @Then /^the response is( not)? JSON$/
     * @param  string     $notJson
     * @return void
     * @throws \Exception
     */
    public function theResponseIsJson($notJson = '')
    {
        $this->processResponse();

        if (strpos($notJson, 'not') === false) {
            if (!$this->responseIsJson) {
                $message = "Response was not JSON\n";
                if (!empty($this->responseDecodeException)) {
                    $message .= $this->responseDecodeException->getMessage();
                }

                throw new \Exception($message."\n".$this->response);
            }
        } else {
            if ($this->responseIsJson) {
                throw new \Exception("Response was JSON\n".$this->response);
            }
        }
    }

    /**
     * @Given /^the response should contain field "([^"]*)"$/
     * @param  string     $name
     * @return void
     * @throws \Exception
     */
    public function theResponseHasAField($name)
    {
        if ($this->responseIsJson) {
            if ($this->associative) {
                if (!(is_array($this->responseData)) || !array_key_exists($name, $this->responseData)) {
                    throw new \Exception('Field "'.$name.'" is not set!');
                }
            } else {
                if (!($this->responseData instanceof stdClass) || !property_exists($this->responseData, $name)) {
                    throw new \Exception('Field "'.$name.'" is not set!');
                }
            }
        } else {
            return new Step\Then('the response is JSON');
        }
    }

    /**
     * @Then /^in the response there is no field called "([^"]*)"$/
     * @param  string     $name
     * @return void
     * @throws \Exception
     */
    public function theResponseShouldNotHaveAField($name)
    {
        if ($this->responseIsJson) {
            if ($this->associative) {
                if (is_array($this->responseData) && array_key_exists($name, $this->responseData)) {
                    throw new \Exception('Field "'.$name.'" should not be there!');
                }
            } else {
                if (($this->responseData instanceof stdClass) && property_exists($this->responseData, $name)) {
                    throw new \Exception('Field "'.$name.'" should not be there!');
                }
            }
        } else {
            return new Step\Then('the response is JSON');
        }
    }

    /**
     * @Then /^field "([^"]+)" in the response should be "([^"]*)"$/
     * @param  string     $fieldName
     * @param  string     $fieldValue
     * @return void
     * @throws \Exception
     */
    public function valueOfTheFieldEquals($fieldName, $fieldValue)
    {
        if ($this->responseIsJson) {
            if (new Step\Given("the response should contain field \"{$fieldName}\"")) {
                $fieldValue = $this->extractValueByGivenLocation($fieldValue) ?: $fieldValue;
                if ($this->associative) {
                    if ($this->responseData[$fieldName] != $fieldValue) {
                        throw new \Exception(
                            sprintf(
                                'Field value mismatch! (given: "%s", match: "%s")',
                                $fieldValue,
                                $this->responseData[$fieldName]
                            )
                        );
                    }
                } else {
                    if ($this->responseData->$fieldName != $fieldValue) {
                        throw new \Exception(
                            sprintf(
                                'Field value mismatch! (given: "%s", match: "%s")',
                                $fieldValue,
                                $this->responseData->$fieldName
                            )
                        );
                    }
                }
            }
        } else {
            return new Step\Then('the response is JSON');
        }
    }

    private function extractValueByGivenLocation($fieldValue)
    {
        if (strpos($fieldValue, '(') !== false) {
            $locationIndex = str_replace(')', '', str_replace('(', '', $fieldValue));

            return $this->locations[$locationIndex];
        }

        return $this->locations[$fieldValue];
    }

    /**
     * @Then /^the response should contain "([^"]*)"$/
     * @param  string     $str
     * @return void
     * @throws \Exception
     */
    public function theResponseShouldContain($str)
    {
        if (!$this->responseIsJson) {
            if (strpos($this->responseData, $str) === false) {
                throw new \Exception(sprintf('String "%s" not found.', $str));
            }
        } else {
            throw new \Exception('Response should not be a JSON message.');
        }
    }

    /**
     * @Then /^field "([^"]+)" in the response should be an? (int|integer) "([^"]*)"$/
     * @param  string     $fieldName
     * @param  string     $type
     * @param  string     $fieldValue
     * @return void
     * @throws \Exception
     * @todo Need to be better designed.
     */
    public function fieldIsOfTypeWithValue($fieldName, $type, $fieldValue)
    {
        if ($this->responseIsJson) {
            if (new Step\Given("the response should contain field \"{$fieldName}\"")) {
                switch (strtolower($type)) {
                    case 'int':
                    case 'integer':
                        if (!preg_match('/^(0|[1-9]\d*)$/', $fieldValue)) {
                            throw new \Exception(
                                sprintf(
                                    'Field "%s" is not of the correct type: %s!',
                                    $fieldName,
                                    $type
                                )
                            );
                        }
                        // TODO: We didn't check if the value is as expected here.
                        break;
                    default:
                        throw new \Exception('Unsupported data type: '.$type);
                        break;
                }
            }
        } else {
            return new Step\Then('the response is JSON');
        }
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     * @param  int        $httpStatus
     * @return void
     * @throws \Exception
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
        if (((string) $this->response->getStatusCode()) !== $httpStatus) {
            throw new \Exception(
                sprintf(
                    'HTTP code does not match %s (actual: %s)',
                    $httpStatus,
                    $this->response->getStatusCode()
                )
            );
        }
    }

    /**
     * @Given /^the response should be "([^"]*)"$/
     * @param  string     $string
     * @return void
     * @throws \Exception
     */
    public function theResponseShouldBe($string)
    {
        $data = $this->response->getBody(true);

        if ($string != $data) {
            throw new \Exception(
                sprintf("Unexpected response.\nExpected response:%s\nActual response:\n%s".$string, $data)
            );
        }
    }

     /**
     * @Then /^items should be in this order: "([^"]*)"$/
     * @param  string     $order
     * @return void
     * @throws \Exception
     */
    public function checkItemsOrder($order)
    {
        if ($this->responseIsJson) {
            $order = explode(',', $order);
            foreach ($order as $key => $value) {
                $order[$key] = $this->extractValueByGivenLocation($value);
            }
            
            $actualOrder = array();
            foreach($this->responseData['items'] as $key => $item) {
                $actualOrder[] = $item['number'];
            }

            if ($order !== $actualOrder) {
                throw new \Exception(
                    sprintf(
                        "Unexpected order.\nExpected order:\n%s\nActual response:\n%s", 
                        json_encode($order), 
                        json_encode($actualOrder)
                    )
                );
            }
        } else {
            return new Step\Then('the response isn\'t JSON');
        }
    }

    /**
     * @Then /^echo last response$/
     * @return void
     */
    public function echoLastResponse()
    {
        $this->printDebug($this->client->getLastRequest()."\n\n".$this->response."\n\n"."access_token: ".$this->access_token);
    }

    /**
     * Return the response object.
     *
     * This public method is also for other context(s) to get and process REST API response.
     *
     * @return Guzzle\Http\Message\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Return the response data.
     *
     * This public method is also for other context(s) to get and process REST API response.
     *
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Decode JSON string.
     *
     * @param  string     $string A JSON string.
     * @return mixed
     * @throws \Exception
     * @see http://www.php.net/json_last_error
     */
    protected function decodeJson($string)
    {
        $json = json_decode($string, $this->associative);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $json;
                break;
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $message = 'Unknown error';
                break;
        }

        throw new \Exception('JSON decoding error: '.$message);
    }
}
