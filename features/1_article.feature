Feature: Article
  I need to be able list and get articles

  Scenario: Get articles from gimme
    When I send a GET request to "/articles"
    Then the response code should be 200
    And response should contain "code": 200
    And response should have "data" with elements
    And response should have "items" with elements under "data"

  Scenario: Get single article from gimme
    When I send a GET request to "/articles/1"
    Then the response code should be 200
    And response should contain "code": 200
    And response should have "data" with elements
    And response shoud have keys "number, title, created, updated, published, webcode, summary, type, keywords, authors, topics, slideshows, fields, renditions" under "data"

  Scenario: Get single article comments from gimme
    When I send a GET request to "/articles/1/comments"
    Then the response code should be 200
    And response should contain "code": 200
    And response should have "data" with elements
    And response should have "items" with elements under "data"