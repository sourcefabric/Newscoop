Feature: Testing linking/unlinkig related articles feature
    I need to be able to link and unlink articles (aka. related articles)

    Scenario: Create articles and link them them
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
        Then save new item location as "base_article"

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
        Then save new item location as "second_article"

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
        Then save new item location as "third_article"


        Given that I want to link an article to the article
            And that i have "link" header with "<$$second_article$$; rel='article'>" value
        When I request "<<base_article>>"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to link an article to the article
            And that i have "link" header with "<$$third_article$$; rel='article'>" value
        When I request "<<base_article>>"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to link an article to the article
            And that i have "link" header with "<$$second_article$$; rel='article'>,<2; rel='article-position'>" value
        When I request "<<base_article>>"
        Then the response status code should be 201
            And the response is JSON        

        Given that I want to unlink an article to the article
            And that i have "link" header with "<$$second_article$$; rel='article'>" value
        When I request "<<base_article>>"
        Then the response status code should be 204
            And the response is JSON