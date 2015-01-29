Feature: Articles
    I need to be able to create and update articles

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
                | keywords         | <<text>>           | 30 |

            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "new_article"

        Given that I want to create an new article
            And that i have fake "article" data:
                | name             | <<sentence>>       | 4 |
                | language         | 1                  ||
                | publication      | 1                  ||
                | issue            |                    ||
                | section          |                    ||
                | comments_enabled | 1                  ||
                | type             | news               ||
                | onFrontPage      | 1                  ||
                | onSection        | 1                  ||
                | keywords         | test keywords      | 30 |
                | fields[content] | <<sentence>>       | 35 |

        When I submit "article" data to "<<new_article>>"
            Then the response status code should be 200
            And the response is JSON
            And field "keywords" in the response should be "test keywords"

    Scenario: Getting all article's topics
        Given that I want to check if there are any topics attached to the article
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "/articles/1/en/topics?query="
        Then the response status code should be 200
            And the response is JSON
            And the response should contain field "items"