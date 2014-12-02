Feature: Author
  I need to be able list all gimme endpoints

  Scenario: Get all gimme endpoints
    When I send a GET request to "/author/7"
    Then the response code should be 200
    And response should have keys "firstName, lastName, image"