Feature: Sections
  I need to be able list and get sections

  Scenario: Get single section from gimme
    When I send a GET request to "/sections"
    Then the response code should be 200
    And response should have "items" with elements
    And response should have item with keys "number, title, articlesLink"

  Scenario: Get single section from gimme
    When I send a GET request to "/sections/20/en/articles"
    Then the response code should be 200
    And response should have keys "id, title, items"
    And response should have "items" with elements