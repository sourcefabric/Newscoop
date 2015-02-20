
<!-- Orange Box -->
<div class="row">
<div class="slider_box people_box hidden-phone span12">
<div class="row">
  <h3 class="EditorialCommentsText">{{'EditorialComments'|translate}}</h3>
  <div class="people_wrapper span12">

    <!-- People / Row (12) -->

    <ul class="people row">
     {{list_playlist_articles id="2" length="8"}}
      <li class="span3">
        <div class="person content_text">
          <header class="header">
           {{list_article_authors length="1"}}
           {{$image = false}}
           {{ if $gimme->author->user->defined && $gimme->author->user->image(61, 82)}}
           {{$image = true}}
           <div class="thumbnail">
             <img alt="{{ $gimme->author->user->uname|escape }}" src="{{ $gimme->author->user->image(61, 82) }}"  />
           </div>
           {{ elseif $gimme->author->picture->imageurl }}
           {{$image = true}}
           <div class="thumbnail">
            <img src="{{ $gimme->author->picture->imageurl }}" alt="{{ $gimme->author->name }}" width="61" />
          </div>
          {{ /if }}


          <h3 class="header_field_2 {{if !$image}} no_margin_left{{/if}}">{{ if $gimme->author->user->defined }}
            <a href="{{ $view->url(['username' => $gimme->author->user->uname], 'user') }}">
              {{/if}}
              {{$gimme->author->name}}
              {{ if $gimme->author->user->defined }}
            </a>
            {{/if}}
          </h3>
          {{/list_article_authors}}
          <h4 class="header_field_3"><a href="{{url options="article"}}">{{$gimme->article->name}}</a></h4>
        </header>

        <p>{{$gimme->article->deck|strip_tags|truncate:200:"...":false}}</p>
      </div>
    </li>
    {{/list_playlist_articles}}

  </ul>
  <!-- End People / Row (12) -->

</div>
</div>
<!-- Slider Navigation -->
<ul class="navigation">
  <li class="arrow arrow_left"><a href="#"></a></li>
  <li class="dot"><a href="#"></a></li>
  <li class="dot current"><a href="#"></a></li>
  <li class="arrow arrow_right"><a href="#"></a></li>
</ul>
<!-- End Slider Navigation -->
</div>
</div>
<!-- End Orange Box -->