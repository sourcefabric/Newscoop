{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">
            
            <div class="title page-title">
            	<h2>{{ #usersIndex# }}</h2>
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
           
            <section class="grid-6 extended-small">
            
    {{ foreach $users as $user }}            
            	<article>
                	<ul class="info">
                    	<li>{{ #memberFrom# }} <span> {{ $user->created }}</span></li>
                        <li>{{ #postsNo# }} {{ $user->posts_count }}</li>
                        <li><a href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ #viewFullProfile# }}</a></li>
                    </ul>
                    <img src="{{ include file="_tpl/user-image.tpl" user=$user width=130 height=130 }}" />
                    <h4><a href="{{ $view->url(['username' => $user->uname], 'user') }}">{{ $user->uname }}</a></h4>
                    <p><em>{{ $user->first_name }} {{ $user->last_name }}</em></p>
                </article>
    {{ /foreach }}                
            
            </section><!-- / 6 articles grid -->

{{include file='_tpl/paginator_control.tpl'}}
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>