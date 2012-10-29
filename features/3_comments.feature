Feature: Comments
  I need to be able list article comments

  Scenario: Get single article comments from gimme
    When I send a GET request to "/articles/77/en/comments"
    Then the response code should be 200
    And response should have keys "items"
    And response should have item with keys "author, subject, message, created"