Feature: Functions
  All API functions should work properly

  Scenario: Sort articles
    When I send a GET request to "/articles?sort[number]=asc"
    Then the response code should be 200
    And first item from response should have key "number" with value 64

  Scenario: Get only 3 items
    When I send a GET request to "/articles?items_per_page=3"
    Then the response code should be 200
    And i should have only "3" items

  Scenario: Partial response for one item
    When I send a GET request to "/articles?items_per_page=1&fields=number"
    Then the response code should be 200
    And i should have only "1" items
    And response should have item with only "number" keys