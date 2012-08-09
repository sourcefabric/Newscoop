Feature: Gimme
  I need to be able list all gimme connections

  Scenario: Response shoud have code : 200
    When I send a GET request to "/"
    Then the response code should be 200
    And response should contain "<article>"