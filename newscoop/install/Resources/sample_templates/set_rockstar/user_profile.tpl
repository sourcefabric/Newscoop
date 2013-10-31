{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">
            
            <div class="title page-title">
            	<h2>{{ #userProfile# }}</h2>
            </div>
            
            <div class="section-search">

    <form method="GET" action="{{ $view->url(['controller' => 'user', 'action' => 'search'], 'default', true) }}">
        <fieldset>
            <input type="text" name="q" value="{{ #inputSearch# }}"></input>
            <input type="submit" class="button" value=""></input>
        </fieldset>
    </form>

<ul>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default', true) }}">{{ #active# }}</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-z'], 'default', true) }}">{{ #all# }}</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-d'], 'default', true) }}">A-D</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'e-k'], 'default', true) }}">E-K</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'l-p'], 'default', true) }}">L-P</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'q-t'], 'default', true) }}">Q-T</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'u-z'], 'default', true) }}">U-Z</a></li>
    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'editors'], 'default', true) }}">{{ #editors# }}</a></li>
</ul>
            
            </div>
            
<section class="grid-1">           

            <ul class="user-slider jcarousel-skin-full">
                <li>
                	<article>
                        <figure>
                            <img src="{{ include file="_tpl/user-image.tpl" user=$user width=290 height=190 }}" />
                            <h3>{{ $user->first_name }} {{ $user->last_name }} ({{ $user->uname }})</h3>
                            <p><em>member from:</em> {{ $user->created }} I <em>{{ #postsNo# }}</em> {{ $user->posts_count }}</p>
                        </figure>
                        <div class="details">
    <p>          
                 
    {{ foreach $profile as $label => $value }} 
    {{ if !empty($value) }}  
    
    {{ if $label == "bio" }}
      <em>{{ #aboutProfile# }}</em>  
      {{ $value|default:"n/a" }}<br /> 
    {{ elseif $label == "birth_date" }}
      <em>{{ #dateOfBirth# }}</em> 
      {{ $value|default:"n/a" }}<br />
    {{ elseif $label == "comment_delivered" }}
      <em>{{ #commentsSent# }}</em>
      {{ $value|default:"n/a" }}<br />   
    {{ elseif $label == "comment_recommended" }}
      <em>{{ #recommComm# }}</em>
      {{ $value|default:"n/a" }}<br />    
    {{ elseif $label == "gender" }}
      <em>{{ #gender# }}</em>
      {{ $value|default:"n/a" }}<br />      
    {{ elseif $label == "organisation" }}
      <em>{{ #organisation# }}</em>
      {{ $value|default:"n/a" }}<br />              
    {{ elseif $label == "website" }}
      <em>{{ #website# }}</em>
      <a rel="nofollow" href="http://{{ $profile['website']|escape:url }}">{{ $profile['website']|escape }}</a><br />      
    {{ /if }}
      
    {{ /if }}    
    {{ /foreach }} 

    </p>                       
                        </div>
                    </article>
                </li>

            </ul><!-- / User slider -->

{{if $user->isAuthor()}}
	{{ include file="_tpl/user-content.tpl" user=$user }}
{{/if}}
                        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>