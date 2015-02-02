Feature: Testing linking/unlinking topics to/from the articles
    In order to link/ublink topics through the API
    As a service user
    I want to see if the topics can be linked or unlinked to/from the articles

    Scenario: Create a new article and topic, then link/unlink topic from created article.
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

        Given that I want to make a new topic
            And that i have fake "topic" data:
                    | title            | <<sentence>>      | 4 |

            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "topic" data to "/topics"
        Then the response status code should be 201
            And the response is JSON
        Then save new item location as "new_topic"

        Given that I want to link that topic to the article
            And that i have "link" header with "<$$new_topic$$; rel='topic'>" value
        When I request "<<new_article>>"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to unlink that topic from article
            And that i have "link" header with "<$$new_topic$$; rel='topic'>" value
        When I request "<<new_article>>"
        Then the response status code should be 204
            And the response is JSON