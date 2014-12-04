Feature: Snippet
    I need to be able list and get snippets

    Scenario: Create a new Snippet Template on the Gimme
        Given that i want to create "snippetTemplate" with name "name" and content "Youtube" with type "text"
        Given that i want to create "snippetTemplate" with name "code" and content "<iframe width='{{ width }}' height='{{ height }}' src='//www.youtube.com/embed/{{ ID }}' frameborder='0' allowfullscreen></iframe>" with type "text"
        When I send a "POST" request to "/snippetTemplates" with custom form data
        Then the response code should be 201
        And response should have header "X-Location"

    Scenario: Get snippets from gimme
        When I send a GET request to "/snippets"
        Then the response code should be 200
        And response should have "items" with elements

    Scenario: Get single snippet from gimme
        When I send a GET request to "/snippets/1"
        Then the response code should be 200
        And response should have keys "templateId, id, template, name, enabled, created, modified"
        And response should have "fields" with elements
        And response should have keys "ID, width, height" under "fields"

    Scenario: Get snippets attached to Article from gimme
        When I send a GET request to "/articles/64/en/snippets"
        Then the response code should be 200
        And response should have "items" with elements
