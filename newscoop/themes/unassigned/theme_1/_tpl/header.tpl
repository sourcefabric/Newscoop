<div class="header_wrap">
  <div class="container">
<!-- Titlebar -->
<nav id="mobilemenu" class="visible-phone">
  <ul class="menu" id="mobile_sections">
    <li {{ if  $gimme->template->name|strstr:"front.tpl"  }} class="current" {{ /if }}><a href="http://{{ $gimme->publication->site }}/">{{'home'|translate}}</a></li>



    {{ local }}
    {{dynamic}}
    {{ set_current_issue }}
    {{ list_sections }}
    <li{{ if ($gimme->section->number == $gimme->default_section->number) }} class="current"{{ /if }}><a href="{{ url options="section" }}" title="{{'viewAllPosts'|translate}} {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
    {{ /list_sections }}

    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}" title="Community index">{{'community'|translate}}</a></li>



  </ul>

  <ul class="menu" id="mobile_lang">
    {{ list_languages of_publication="true" }}
      <li {{ if $gimme->language == $gimme->default_language }} class="current"{{ /if }}><a href="{{ url }}" >{{ $gimme->language->name }}</a></li>
    {{ /list_languages }}
    <!-- Delete those below. It is just a sample -->
    <li><a href="#">German</a></li>
    <li><a href="#">Polish</a></li>
  </ul>
  {{/dynamic}}
<div class="mobile-search">

    {{ search_form template="search.tpl" submit_button="" }}
    {{ camp_edit object="search" attribute="keywords" html_code="placeholder=\"{{'search'|translate}}\"" }}
    {{ /search_form }}

</div>



  </nav>
<header id="titlebar">


  <div class="search_area hidden-phone" >

    {{ search_form template="search.tpl" submit_button="" }}
    {{ camp_edit object="search" attribute="keywords" html_code="placeholder=\"{{'search'|translate}}\"" }}
    {{ /search_form }}



  </div>
  {{dynamic}}
  <span class="icon_link icon_lang open_lang_popup hidden-phone tooltip">
    {{'Language'|translate}}
    <ul class="languages">
      {{ list_languages of_publication="true" }}
        <li><a href="{{ url }}" {{ if $gimme->language == $gimme->default_language }} class="active"{{ /if }}>{{ $gimme->language->name }}</a></li>
      {{ /list_languages }}
      <!-- Delete those below. It is just a sample -->
      <li><a href="#">German</a></li>
      <li><a href="#">Polish</a></li>
    </ul>
  </span>

  {{ if !$gimme->user->logged_in }}
  <a href="{{ $view->url(['controller' => 'register', 'action' => 'index'], 'default') }}" class="icon_link icon_padlock hidden-phone" id="registerButtonFront">{{'Register'|translate}}</a>

  <a href="#" class="icon_link icon_key open_login_popup hidden-phone">{{'Login'|translate}}</a>

<div class="logininfo" style="display:none">
  <a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}" class="icon_link hidden-phone">{{'myprofile'|translate}}</a>

    <a href="{{ $view->url(['controller' => 'auth', 'action' => 'logout'], 'default') }}?t={{ time() }}" class="icon_link icon_key hidden-phone">{{'logout'|translate}}</a>
</div>
  {{else}}
  <a href="{{ $view->url(['controller' => 'dashboard', 'action' => 'index'], 'default') }}" class="icon_link hidden-phone">{{'myprofile'|translate}}</a>

    <a href="{{ $view->url(['controller' => 'auth', 'action' => 'logout'], 'default') }}?t={{ time() }}" class="icon_link icon_key hidden-phone">{{'logout'|translate}}</a>
  {{/if}}



  {{/dynamic}}




  <button class="navbar-toggle visible-phone" type="button" id="mobilemenuopen" >
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <span id="mobilelangopen" class="icon_link icon_lang mobile-lang visible-phone ">
        </span>

</header>
<!-- End Titlebar -->

<!-- Top -->
<header id="top" class="padding_bottom_10">
  <div class="top_content">
    <a href="http://{{ $gimme->publication->site }}" id="logo" title="{{ $gimme->publication->name }}"></a>
    <a target="_blank" href="https://www.sourcefabric.org" class="add">
                  <img src="{{ url static_file='_img/ads/top.png' }}" alt="" />
                </a>
   </div>

 </header>
 <!-- End Top -->
   </div>
 </div>



<div class="wrap_menu_wrap hidden-phone">
 <div class="menu_wrap">
   <div class="container">
 <!-- Top Menu -->
 <nav id="top_menu" class="hidden-phone" >
  <ul class="menu">
    <li {{ if  $gimme->template->name|strstr:"front.tpl"  }} class="current" {{ /if }}><a href="http://{{ $gimme->publication->site }}/">{{'home'|translate}}</a></li>





    {{ list_sections }}
    <li{{ if ($gimme->section->number == $gimme->default_section->number) }} class="current"{{ /if }}><a href="{{ url options="section" }}" title="{{'viewAllPosts'|translate}} {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
    {{ /list_sections }}

    <li><a href="{{ $view->url(['controller' => 'user', 'action' => 'index'], 'default') }}" title="{{'community'|translate}}">{{'community'|translate}}</a></li>



  </ul>
</nav>
<!-- End Top Menu -->



{{/local}}

  </div>
</div>
</div>