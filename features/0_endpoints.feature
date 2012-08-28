Feature: Endpoints
  I need to be able list all gimme endpoints

  Scenario: Get all gimme endpoints
    When I send a GET request to "/"
    Then the response code should be 200
    And response should contain "code": 200
    And response should have "data" with elements
    And response shoud have keys "name, url" under "data"