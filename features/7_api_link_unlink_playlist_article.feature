Feature: Testing linking/unlinkig articles to playlists
    I need to be able to link/unlink article to given playlist

    Scenario: Create articles and attach them to playlists

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
        Then save new item location as "article_1"
        And save "number" field under location "article_1_number"

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

        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "article_2"
        And save "number" field under location "article_2_number"

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

        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "article_3"
        And save "number" field under location "article_3_number"

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

        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "article_4"
        And save "number" field under location "article_4_number"

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

        When I submit "article" data to "/articles/create"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "number"
            And the response should contain field "title"
            And the response should contain field "type"
        Then save new item location as "article_5"
        And save "number" field under location "article_5_number"

        Given that I want to make a new playlist
            And that i have fake "playlist" data:
                    | name            | <<sentence>>      | 4 |

        When I submit "playlist" data to "/articles-lists"
        Then the response status code should be 200
            And the response is JSON
        Then save new item location as "playlist"

        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_1$$; rel='article'>" value
        When I request "<<playlist>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_3$$; rel='article'>" value
        When I request "<<playlist>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_4$$; rel='article'>" value
        When I request "<<playlist>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_2$$; rel='article'>" value
        When I request "<<playlist>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_5$$; rel='article'>" value
        When I request "<<playlist>>"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to check if playlist has been created successfully
        When I request "<<playlist>>/articles"
        Then the response status code should be 200
            And the response is JSON
            And the response should contain field "id"
            And the response should contain field "title"
            And the response should contain field "items"
            And items should be in this order: "article_5_number,article_2_number,article_4_number,article_3_number,article_1_number"

        Given that I want to make a new playlist
            And that i have fake "playlist" data:
                    | name            | <<sentence>>      | 4 |
        When I submit "playlist" data to "/articles-lists"
        Then the response status code should be 200
            And the response is JSON
        Then save new item location as "playlist_2"

        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_1$$; rel='article'>" value
        When I request "<<playlist_2>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_2$$; rel='article'>" value
        When I request "<<playlist_2>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_3$$; rel='article'>" value
        When I request "<<playlist_2>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_4$$; rel='article'>" value
        When I request "<<playlist_2>>"
        Then the response status code should be 201
            And the response is JSON
        Given that I want to link that previously created article to the playlist
            And that i have "link" header with "<$$article_5$$; rel='article'>" value
        When I request "<<playlist_2>>"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to check if playlist has been created successfully
        When I request "<<playlist_2>>/articles"
        Then the response status code should be 200
            And the response is JSON
            And the response should contain field "id"
            And the response should contain field "title"
            And the response should contain field "items"
            And items should be in this order: "article_5_number,article_4_number,article_3_number,article_2_number,article_1_number"
