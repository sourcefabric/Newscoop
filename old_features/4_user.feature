Feature: Users
  I need to be able list and get users

  Scenario: Get users from gimme
    When I send a GET request to "/users"
    Then the response code should be 200
    And response should have "items" with elements

  Scenario: Get single user from gimme
    When I send a GET request to "/users/39"
    Then the response code should be 200
    And response should have keys "email, username, firstName, lastName, attributes"
    And response should have "attributes" with elements
