Feature: Slideshow
  I need to be able list and get slideshows

  Scenario: Get single slideshow from gimme
    When I send a GET request to "/slideshows/5"
    Then the response code should be 200
    And response should have keys "id, title, items"
    And response should have "items" with elements
    And response should have item with keys "caption, type, link"