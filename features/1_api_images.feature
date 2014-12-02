Feature: Images
    I need to be able to work with images api

    Scenario: Check if route is correct
        Given that I want to find an articles
            When I request "/images"
            And the response is JSON

    Scenario: Create new image
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

        Given that I want to find an article
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "<<new_image>>"
            Then the response status code should be 200
            And the response is JSON
            And the response should contain field "photographer"
            And the response should contain field "photographerUrl"
            And the response should contain field "description"

        Given that I want to create an new image
            And that i have fake "image" data:
                | description      | <<sentence>>       | 12 |
                | photographer     | <<name>>           | |
                | photographer_url | <<url>>            | |
                | place            | <<address>>        | |

            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "image" data to "<<new_image>>"
            Then the response status code should be 200
            And the response is JSON
            And the response should contain field "photographer"
            And the response should contain field "photographerUrl"
            And the response should contain field "description"

        Given that I want to delete an image
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "<<new_image>>"
            Then the response status code should be 204
            And the response is JSON