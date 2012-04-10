        <div id="top" class="clearfix">
            <h3>Welcome to the Rockstar Magazine</h3>
            <div class="top-menu">
                <ul>
                
                    {{ dynamic }}
                    {{ if $gimme->user->logged_in }}
                    <li class="login"><a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">{{ $gimme->user->first_name }} {{ $gimme->user->last_name }}</a>
                <ul class="sub">
                           <li><a href="{{ $view->url(['controller' => 'auth', 'action' => 'logout'], 'default') }}?t={{ time() }}">Logout</a></li>
                        </ul>                    
                    </li>
                    {{ else }}
                    <li class="login"><a href="{{ $view->url(['controller' => 'auth', 'action' =>'index'], 'default') }}">Login/SignUp</a></li>
                    {{ /if }}
                    {{ /dynamic }}                 

                    <li class="follow"><a href="#">Follow Us</a>
                      <ul class="sub">
                          <li><a href="#">Follow at Twitter</a></li>
                          <li><a href="#">Like on Facebook</a></li>
                          <li><a href="http://{{ $gimme->publication->site }}/static/rss">Sign up to RSS</a></li>
                        </ul>
                    </li>
                    <li class="share"><a href="#">Share This</a>
                      <ul class="sub">
                          <li><a href="#">Tweet on Twitter</a></li>
                          <li><a href="#">Post to Facebook</a></li>
                          <li><a href="#">Share at Google+</a></li>
                        </ul>
                    </li>
                    {{*
                    <li class="language"><a href="#">Language</a>
                      <ul class="sub">
                          <li><a href="#">English</a></li>
                          <li><a href="#">Deutsch</a></li>
                          <li><a href="#">Nederlands</a></li>
                        </ul>
                    </li>
                    *}}
                </ul>
                {{ search_form template="search.tpl" html_code="class=\"search-box\"" button_html_code="class=\"button\"" }} 
                   {{ camp_edit object="search" attribute="keywords" html_code="placeholder=\"input search\"" }}
                {{ /search_form }}                  
            </div>
        </div><!-- / Top -->
        
        <div id="header" class="clearfix">
        <a href="http://{{ $gimme->publication->site }}">
            <h1>{{ $gimme->publication->name }}</h1>
            <h4>{{ $siteinfo.description }}</h4>
        </a>
            <section class="grid-3-top">
{{ local }}
{{ unset_topic }}            
{{ list_articles length="3" ignore_issue="true" ignore_section="true" order="bydate desc" constraints="highlight is on" }}            
                <article>
                  <small><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a> {{ list_article_topics }}{{ if $gimme->current_list->at_beginning }}<em>/ {{ /if }}<a href="{{ url options="template topic.tpl" }}">{{ $gimme->topic->name }}</a>{{ if $gimme->current_list->at_end }}</em>{{ else }}, {{ /if }}{{ /list_article_topics }}</small>
                    <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></h4>
                    <span class="date">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }}</span>
                </article>
{{ /list_articles }}   
{{ /local }}             
            </section>
        
        </div><!-- / Header -->
        
        <div id="nav-bar" class="clearfix">
          <ul>
              <li{{ if $gimme->template->name == "front.tpl" }} class="active"{{ /if }}><a href="/">Home</a></li>
    {{ local }}
    {{ set_current_issue }}
    {{ list_sections }}              
               <li{{ if $gimme->section->number == $gimme->default_section->number }} class="active"{{ /if }}><a href="{{ uri options="section" }}" title="View all posts filed under {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
    {{ /list_sections }}
    {{ /local }}
            </ul>
            <span class="right"><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}" title="Community index">Community</a></span>            
            
        </div><!-- / Nav Bar -->