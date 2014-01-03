Feature: Topic
  I need to be able list and get topics

  Scenario: Get topics from gimme
    When I send a GET request to "/topics"
    Then the response code should be 200
    And response should have "items" with elements
    And response should have item with keys "id, title, articlesLink"

  Scenario: Get single topic from gimme
    When I send a GET request to "/topics/18/en/articles"
    Then the response code should be 200
    And response should have keys "id, title, items"
    And response should have "items" with elements
