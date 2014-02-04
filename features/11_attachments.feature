Feature: Attachemnts
  Scenario: I want to create, update and delete attachment
    Given that i want to create "attachment" with name "attachment" and content "picture.jpg" with type "file"
    Given that i want to create "attachment" with name "description" and content "Test attachment description" with type "text"
    Given that i want to create "attachment" with name "name" and content "Test attachment name" with type "text"
    Given that i want to create "attachment" with name "language" and content "1" with type "text"
    When I send a "POST" request to "/attachments" with custom form data
    Then the response code should be 201
    And response should have header "X-Location"

    When I send a "GET" request to "last resource" with custom form data
    Then the response code should be 200
    And response should have keys "id, name, extension, mimeType, contentDisposition, sizeInBytes, description, updated, created, source, status"
    Then response should have key "description" with value "Test attachment description"
    Then response should have key "name" with value "Test attachment name"

    Given that i want to create "attachment" with name "attachment" and content "picture.jpg" with type "file"
    Given that i want to create "attachment" with name "description" and content "Test attachment updated" with type "text"
    Given that i want to create "attachment" with name "name" and content "Test attachment name updated" with type "text"
    Given that i want to create "attachment" with name "language" and content "1" with type "text"
    When I send a "POST" request to "last resource" with custom form data
    Then the response code should be 200
    And response should have header "X-Location"

    When I send a "GET" request to last resource
    Then the response code should be 200
    And response should have keys "id, name, extension, mimeType, contentDisposition, sizeInBytes, description, updated, created, source, status"
    Then response should have key "description" with value "Test attachment updated"
    Then response should have key "name" with value "Test attachment name updated"

    When I send a "DELETE" request to last resource
    Then the response code should be 204