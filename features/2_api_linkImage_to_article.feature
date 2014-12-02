Feature: Images
    I need to be able to work link images to articles

    Scenario: Create new article and image
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

        Given that I want to create an new image
            And that i have fake "image" data:
                | description      | <<sentence>>       | 6 |
                | photographer     | <<name>>           | |
                | photographer_url | <<url>>            | |
                | place            | <<address>>        | |
                | image            | <<image>>          | /tmp,640,480 |

            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "image" data to "/images"
            Then the response status code should be 201
            And the response is JSON
            And the response should contain field "photographer"
            And the response should contain field "photographerUrl"
            And the response should contain field "description"
        Then save new item location as "new_image"

        Given that I want to link an image to article
            And that i have "link" header with "<$$new_image$$; rel='image'>" value
        When I request "<<new_article>>"
            Then the response status code should be 201
            And the response is JSON

        Given that I want to unlink an image from article
            And that i have "link" header with "<$$new_image$$; rel='image'>" value
        When I request "<<new_article>>"
            Then the response status code should be 204
            And the response is JSON

        Given that I want to delete an image
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "<<new_image>>"
            Then the response status code should be 204
            And the response is JSON