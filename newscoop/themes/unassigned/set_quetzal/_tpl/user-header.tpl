<header class="section-header">
    <div class="container">
        <div class="row">
            <div class="span10">
                <div class="breadcrumbs">
                    <h2>{{ #userIndex# }}</h2>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="span12 more-news-tabs tab-sections">
                <a class="back-link visible-phone" href="javascript:history.back()">&larr; {{ #back# }}</a>
                <nav class="internal-nav hidden-phone">
                    <ul>
                        <li class="active"><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default', true) }}">Active</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-z'], 'default', true) }}">All</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'a-d'], 'default', true) }}">A-D</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'e-k'], 'default', true) }}">E-K</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'l-p'], 'default', true) }}">L-P</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'q-t'], 'default', true) }}">Q-T</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'filter', 'f' => 'u-z'], 'default', true) }}">U-Z</a></li>
                        <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'editors'], 'default', true) }}">{{ #editors# }}</a></li> 
                    </ul>
                </nav>
            </div>
        </div>                       
    </div>
</header> 
