     <div id="login-box"> 
     
                    {{ dynamic }}
                    {{ if $gimme->user->logged_in }}
                    <p class="fields">Welcome, <a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}">{{ $gimme->user->first_name }} {{ $gimme->user->last_name }}</a> | <a href="{{ $view->url(['controller' => 'auth', 'action' => 'logout'], 'default') }}?t={{ time() }}">logout</a></p>
                    {{ else }}
                    <li><a href="{{ $view->url(['controller' => 'auth', 'action' =>'index'], 'default') }}">Log in (and get involved!)</a></li>
                    {{ /if }}
                    {{ /dynamic }}     

    </div><!-- /#login-box -->          