Feature: Testing linking/unlinkig topics (follow/unfollow topics by users)
    I need to be able to follow/unfollow topics by given users

    Scenario: Create a new topic and link it to the user, unlink and delete
    	Given that I want to make a new topic
	        And that i have fake "topic" data:
	                | title            | <<sentence>>      | 4 |

	        And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
	   	When I submit "topic" data to "/topics"
        Then the response status code should be 201
            And the response is JSON
        Then save new item location as "new_topic"

        Given that I want to link that previously created topic to the user
            And that i have "link" header with "<$$new_topic$$; rel='topic'>" value
        When I request "/users/9"
        Then the response status code should be 201
            And the response is JSON

        Given that I want to check if there are any topics by given user
			And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		When I request "/users/9/topics"
		Then the response status code should be 200
        	And the response is JSON
        	And the response should contain field "items"
        When I request "/users/9"
		Then the response status code should be 200
        	And the response is JSON
        	And the response should contain field "topics"

       	Given that I want to unlink that previously created topic from the user
            And that i have "unlink" header with "<$$new_topic$$; rel='topic'>" value
        When I request "/users/9"
        Then the response status code should be 204
            And the response is JSON

        Given that I want to delete an previously created topic
            And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "<<new_topic>>"
            Then the response status code should be 204
            And the response is JSON

        Given that I want to check if topic was successfully unassigned from the user after deletion
        	And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I request "/users/9"
		Then the response status code should be 200
        	And the response is JSON
        	And in the response there is no field called "topics"
