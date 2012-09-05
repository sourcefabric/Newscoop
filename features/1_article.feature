Feature: Article
  I need to be able list and get articles

  Scenario: Get articles from gimme
    When I send a GET request to "/articles?method=option"
    Then the response code should be 200
    And response should contain json:
    """
    {
      "/articles":"<base_url>articles",
      "/articles/{number}":"<base_url>articles/1",
      "/articles/{number}/{language}/comments":"<base_url>articles/1/en/comments"
    }
    """

  Scenario: Get articles from gimme
    When I send a GET request to "/articles"
    Then the response code should be 200
    And response should have "items" with elements

  Scenario: Get single article from gimme
    When I send a GET request to "/articles/74"
    Then the response code should be 200
    And response should have keys "number, title, updated, published, authors"

  Scenario: Get single article comments from gimme
    When I send a GET request to "/articles/77/en/comments"
    Then the response code should be 200
    And response should have keys "items"
    And response should have item with keys "author, subject, message, created"