Feature: Testing Topics API
	In order to maintain topics through the API
	As a service user
    I want to see if the topics management works as expected

    Scenario: Checking topics endpoint
        When I request "/topics"
        Then the response is JSON

    Scenario: Creating a new root topic and checking if it has been created successfully
    	Given that I want to make a new topic
	        And that i have fake "topic" data:
	                | title            | roottopic      | 4 |

	        And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "topic" data to "/topics"
        Then the response status code should be 201
            And the response is JSON
        Then save new item location as "root_topic"

    	Given that I want to check if root topic has been created successfully
    		When I request "<<root_topic>>"
	    	Then the response status code should be 200
	            And the response is JSON
		        And the response should contain field "id"
	            And the response should contain field "title"
	            And the response should contain field "left"
	            And the response should contain field "right"
	            And the response should contain field "root"
	            And the response should contain field "level"
	            And the response should contain field "translations"
				And field "level" in the response should be "0"
				And field "title" in the response should be "roottopic"

    Scenario: Creating a new subtopic and checking if it has been created successfully
    	Given that I want to make a new subtopic
	        And that i have fake "topic" data:
	                | title            | <<sentence>>       | 4 |
	                | parent           | 1                  ||

	        And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "topic" data to "/topics"
        Then the response status code should be 201
            And the response is JSON
        Then save new item location as "sub_topic"

    	Given that I want to check if subtopic has been created successfully
    		When I request "<<sub_topic>>"
	    	Then the response status code should be 200
	            And the response is JSON
		        And the response should contain field "id"
	            And the response should contain field "title"
	            And the response should contain field "parent"
	            And the response should contain field "left"
	            And the response should contain field "right"
	            And the response should contain field "root"
	            And the response should contain field "level"
	            And the response should contain field "translations"
				And field "parent" in the response should be "1"
				And field "level" in the response should be "1"

	Scenario: Getting all the topics
		Given that I want to check if there are any topics
			And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		When I request "/topics"
		Then the response status code should be 200
        	And the response is JSON
       		And the response should contain field "items"

    Scenario: Creating a new topic and attaching it directly to the article
		Given that I want to make a new topic and attach it to the article
			And that i have fake "topic" data:
	                | title            | <<sentence>>       | 4 |

	        And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		When I submit "topic" data to "/articles/1/en/topics"
		Then the response status code should be 201
        	And the response is JSON
        Then save new item location as "new_topic"

        Given that I want to check if topic has been created successfully
    		When I request "<<new_topic>>"
	    	Then the response status code should be 200
	            And the response is JSON
		        And the response should contain field "id"
	            And the response should contain field "title"
	            And the response should contain field "left"
	            And the response should contain field "right"
	            And the response should contain field "root"
	            And the response should contain field "level"
	            And the response should contain field "translations"
				And field "level" in the response should be "0"

    Scenario: Getting all the topics attached to a given article
		Given that I want to check if there are topics attached to the article
		When I request "/topics/article/1/en"
			And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		Then the response status code should be 200
        	And the response is JSON

    Scenario: Getting the list of the topics by given search query
		Given that I want to find an topic by given title
			And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		When I request "/search/topics?query=roottopic"
		Then the response status code should be 200
        	And the response is JSON
       		And the response should contain field "items"

    Scenario: Getting articles by given topic
		Given that I want to find an articles by given topic
			And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
		When I request "/topics/1/en/articles"
		Then the response status code should be 200
        	And the response is JSON
       		And the response should contain field "items"

    Scenario: Creating a new root topic with the title that already exist
    	Given that I want to make a new topic
	        And that i have fake "topic" data:
	                | title            | roottopic      | 4 |

	        And I'm logged in as "testuser" with "testpassword" with client "1_svdg45ew371vtsdgd29fgvwe5v" and secret "h48fgsmv0due4nexjsy40jdf3sswwr"
        When I submit "topic" data to "/topics"
        Then the response status code should be 409
            And the response is JSON
