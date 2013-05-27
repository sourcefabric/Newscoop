{{ list_articles length="1" ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5 type is page" }}             
            	<article>
                	<h2>{{ #aboutUs# }}</h2>
                    <h3>{{ #missionStatement# }}</h3>
                    <p>{{ $gimme->article->full_text|strip_tags:false|truncate:250 }} </p>
                </article>
{{ /list_articles }}