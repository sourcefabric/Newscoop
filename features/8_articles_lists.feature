Feature: Articles Lists
  I need to be able list and get articles lists

  Scenario: Get single articles-lists from gimme
    When I send a GET request to "/articles-lists"
    Then the response code should be 200
    And response should have "items" with elements
    And response should have item with keys "id, title, articlesLink"

  Scenario: Get single slideshow from gimme
    When I send a GET request to "/articles-lists/1/articles"
    Then the response code should be 200
    And response should have keys "id, title, items"
    And response should have "items" with elements