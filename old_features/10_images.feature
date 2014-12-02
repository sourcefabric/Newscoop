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

  Scenario: I want to create, update and delete image
    Given that i want to create "image" with name "image" and content "picture.jpg" with type "file"
    Given that i want to create "image" with name "description" and content "Test Image" with type "text"
    Given that i want to create "image" with name "photographer" and content "John Doe" with type "text"
    Given that i want to create "image" with name "photographer_url" and content "newscoop.dev" with type "text"
    Given that i want to create "image" with name "place" and content "Prague" with type "text"
    When I send a "POST" request to "/images" with custom form data
    Then the response code should be 201
    And response should have header "X-Location"

    Given that i want to create "image" with name "image" and content "picture.jpg" with type "file"
    Given that i want to create "image" with name "description" and content "Test Image updated" with type "text"
    Given that i want to create "image" with name "photographer" and content "John Doe updated" with type "text"
    Given that i want to create "image" with name "photographer_url" and content "newscoop.dev updated" with type "text"
    Given that i want to create "image" with name "place" and content "Prague updated" with type "text"
    When I send a "POST" request to "last resource" with custom form data
    Then the response code should be 200
    And response should have header "X-Location"

    When I send a "GET" request to last resource
    Then the response code should be 200
    And response should have keys "id, location, basename, thumbnailPath, url, description, width, height, photographer, photographerUrl, place"
    Then response should have key "description" with value "Test Image updated"
    Then response should have key "photographer" with value "John Doe updated"
    Then response should have key "photographerUrl" with value "newscoop.dev updated"
    Then response should have key "place" with value "Prague updated"

    When I send a "DELETE" request to last resource
    Then the response code should be 204