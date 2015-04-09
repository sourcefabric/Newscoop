Feature: Endpoints
    I need to be able to create and search articles

    Scenario: Check if route is correct
        Given that I want to find an articles
            When I request "/articles"
            And the response is JSON

    Scenario: Create new article
        Given that I want to create an new article
            And that i have fake "article" data:
                | name             | <<sentence>>       | 4 |
                | language         | 1                  ||
                | publication      | 1                  ||
                | issue            |                    ||
                | section          |                    ||
                | comments_enabled | 1                  ||
                | type             | news               ||
                | onFrontPage      | 0                  ||
                | onSection        | 0                  ||
                | keywords         | unique keyword       | |

            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "new_article"

        # we need publish that article first to make it searchable

        Given that I want to find an article
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "/search/articles?query=unique keyword"
            Then the response status code should be 200
            And the response is JSON
            And the response should contain field "items"