{{ include file="_tpl/_html-head.tpl" }}

<body>

  <div id="container">
      
{{ include file="_tpl/header.tpl" layout="true" }}
    
    <div class="row clearfix" role="main">    
    
      <div id="maincol" class="community eightcol clearfix">
    
        {{ block content }}{{ /block }}
        
      </div><!-- /#maincol -->
        
      <div id="sidebar" class="community fourcol last">
      
      {{ include file="_tpl/sidebar-loginbox.tpl" }} 

{{ if !($userindex == 1) }}                  
{{ if $user->isAuthor() }}
<h3>About {{ $user->first_name }}</h3>
<dl class="profile">
    {{ foreach $profile as $label => $value }} 
    {{ if !empty($value) }}

    {{ if $label == "bio" }}
      <dd>{{ $value }}</dd>
      
    {{ elseif $label == "birth_date" }}
      <dt>Date of birth:</dt>
      <dd>{{ $value|default:"n/a" }}</dd>
      
    {{ elseif $label == "comment_delivered" }}
      <dt>Comments sent:</dt>
      <dd>{{ $value|default:"n/a" }}</dd>
      
    {{ elseif $label == "comment_recommended" }}
      <dt>Recommended comments:</dt>
      <dd>{{ $value|default:"n/a" }}</dd>
    
    {{ elseif $label == "gender" }}
      <dt>Gender:</dt>
      <dd>{{ $value|default:"n/a" }}</dd>
    
    {{ elseif $label == "organisation" }}
      <dt>Organisation:</dt>
      <dd>{{ $value|default:"n/a" }}</dd>
    
    {{ elseif $label == "website" }}
      <dt>Website:</dt>
      <dd><a rel="nofollow" href="http://{{ $profile['website']|escape:url }}">{{ $profile['website']|escape }}</a></dd>
    {{ /if }}       
       
    {{ /if }}
    {{ /foreach }}
</dl>

{{ include file="_tpl/sidebar-community-feed.tpl" }}  

{{ else }}
 
{{ include file="_tpl/sidebar-community-feed.tpl" }}  
       
{{ include file="_tpl/_banner-sidebar.tpl" }} 

{{ /if }}

{{ else }}

{{ include file="_tpl/sidebar-community-feed.tpl" }}  
            
{{ include file="_tpl/_banner-sidebar.tpl" }}   

{{ /if }} 
            
        </div><!-- /#sidebar -->
    
    </div>
    
{{ include file="_tpl/footer.tpl" }}

  </div> <!-- /#container -->

{{ include file="_tpl/_html-foot.tpl" }}
