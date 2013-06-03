<h1>{{ #issuesArchive# }}</h1>

{{ list_issues order="bynumber desc" constraints="number greater 1" }} 

          <article class="clearfix">
                <header>
                  <hgroup>
                        <h3><a href="{{ uri options="template issue.tpl" }}">{{ $gimme->issue->name }}</a></h2>
                        <h4>{{ #publishedOn# }} <time datetime="{{ $gimme->issue->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->issue->publish_date|camp_date_format:"%d %M %Y" }}</time></h3> 
                    </hgroup>
                </header>
            </article>

{{ /list_issues }}    
