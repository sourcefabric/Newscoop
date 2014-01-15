Feature: Images
  I need to be able list and get images

  Scenario: Get single image from gimme
    When I send a GET request to "/images"
    Then the response code should be 200
    And response should have "items" with elements
    And response should have item with keys "id, basename"

  Scenario: Get single slideshow from gimme
    When I send a GET request to "/images/8"
    Then the response code should be 200
    And response should have keys "id, location, basename, thumbnailPath, url, description, width, height, photographer, photographerUrl, place"

  Scenario: I want to create new image
    Given that i want to create new Image
    And that basename is 'testimage.jpg'
    When I send a POST request to "/images"
    Then the response code should be 200
    And response should have keys "id, location, basename, thumbnailPath, url, description, width, height, photographer, photographerUrl, place"
