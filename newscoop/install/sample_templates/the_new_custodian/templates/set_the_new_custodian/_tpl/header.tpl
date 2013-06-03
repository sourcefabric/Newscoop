    <header id="global" class="row">
            <ul id="secnav" class="clearfix right">
            	 {{ local }}
    				 {{ unset_topic }}
                {{ list_articles ignore_issue="true" ignore_section="true" constraints="issue is 1 section is 5"}}
                <li><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
                {{ /list_articles }} 
                {{ /local }}         
                <li><a href="{{ uri options="template archive.tpl" }}">{{ #archives# }}</a></li>
            </ul><!-- /#secnav -->
    
        <div class="row">
            <hgroup class="eightcol">
                <h1><a href="http://{{ $gimme->publication->site }}"><img alt="Home" src="{{ url static_file='_img/logo.png' }}" /></a></h1>         
            </hgroup>
            <div class="fourcol last">
                <div id="search-box">
                    {{ search_form template="search.tpl" submit_button="search" }} 
                    {{ camp_edit object="search" attribute="keywords" html_code="placeholder=\"{{ #keywords# }}\"" }}
                    {{ /search_form }}        
                </div><!-- /#search-box -->            
            </div> 
        </div>   
                
        <nav id="main">
        	<div class="nav-header">
            	<a data-toggle="collapse" data-target=".nav-collapse">Navigation</a> 
            </div>
            <div class="nav-collapse collapse in">
                <ul>
                  <li{{ if $gimme->template->name == "front.tpl" }} class="selected"{{ /if }}><a href="/">{{ #home# }}</a></li>
    {{ local }}
    {{ set_current_issue }}
    {{ list_sections }}              
                  <li{{ if ($gimme->section->number == $gimme->default_section->number) && ($gimme->template->name == "section.tpl" || $gimme->template->name == "article.tpl") }} class="selected"{{ /if }}><a href="{{ uri options="section" }}" title="{{ #viewAllPosts# }} {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
    {{ /list_sections }}
    {{ /local }}
                  <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}" title="Community index">{{ #community# }}</a></li>
              </ul>
          </div>
        </nav>
       <ul class="clearfix" id="breadcrumbs">
          <li>{{ #youAreHere# }}</li>
          <li><a href="/">{{ #home# }}</a></li>
          {{ if $layout == "true" }}<li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}">{{ #community# }}</a></li>
          {{ elseif $gimme->template->name == "search.tpl" }}<li><a href="{{ url }}">{{ #search# }}</a></li>
          {{ elseif $gimme->template->name == "topic.tpl" }}<li><a href="{{ url }}">{{ #topic# }}</a></li>
          {{ elseif $gimme->template->name == "archive.tpl" }}<li><a href="{{ url }}">{{ #archive# }}</a></li>
          {{ elseif $gimme->template->name == "issue.tpl" }}<li><a href="{{ url options="issue" }}">{{ $gimme->issue->name }}</a></li>
          {{ elseif $gimme->template->name == "404.tpl" }}<li><a href="{{ url }}">{{ #errorPage# }}</a></li>
          {{ else }}
          	{{ if $gimme->section->defined }}<li><a href="{{ url options="section" }}">{{ $gimme->section->name }}</a></li>{{ /if }}
          	{{ if $gimme->article->defined }}<li><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a></li>{{ /if }} 	       
          {{ /if }}	 
       </ul><!-- /#breadcrumbs -->
    </header>