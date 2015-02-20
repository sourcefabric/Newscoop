{{$user}}
{{dynamic}}
{{ if $user->isAuthor() }}

{{ $escapedName=str_replace(" ", "\ ", $user->author->name) }}
<div class="bloger_news_items clear">
  <div>
    <ul>

      {{ list_articles ignore_publication="true" ignore_issue="true" ignore_section="true" constraints="author is `$escapedName` type is news"  order="ByPublishDate desc" length="5"  }}
      <li class="news_item">
	     {{ image rendition="section" }}
	     <a href="{{url options="article"}}" class="thumbnail">
	       <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" class="small" />
	     </a>
	     {{/image}}
        <div class="content content_text">

         <h6 class="info">{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>
          <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->title}}</a></h3>
          <p>{{$gimme->article->deck|strip_tags}}</p>
        </div>
      </li>


      {{if $gimme->current_list->at_end}}
          </ul>
        </div>
      </div>








      {{ if $gimme->current_list->count > $gimme->current_list->length }}

      {{ $page=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
      {{ $list_id=$gimme->current_list_id() }}

      <nav class=" pagination">

        {{ unset_article }}

        {{ if $gimme->current_list->has_previous_elements }}

        <a href="?{{ urlparameters options="previous_items" }}" class="arrow arrow_left" title="">{{'previous'|translate}}</a>


        {{/if}}

        {{assign var="allPages" value=($gimme->current_list->count/5)|ceil  }}
        {{assign var="currentPage" value=$page/5}}




        {{assign var="firstToShow" value=$currentPage-2}}
        {{assign var="lastToShow" value=$currentPage+5}}

        {{if $firstToShow < 1 }}
        {{assign var="firstToShow" value=1}}
        {{assign var="lastToShow" value=$lastToShow+3}}
        {{/if}}

        {{if $lastToShow > $allPages }}
        {{assign var="lastToShow" value=$allPages+1}}
        {{/if}}

        {{if $lastToShow-$firstToShow>0}}

         <ul>

           {{if $firstToShow>1}}
           <li class="firstlast"><a href="?{{$list_id}}=0">1</a></li>
           <li class="firstlast">...</li>
           {{/if}}
           {{for $foo=$firstToShow to $lastToShow}}


                 {{if $foo-1==$currentPage}}

                 <li class="current"><a>{{ $foo }}</a></li>

                 {{else}}
                 <li><a href="?{{$list_id}}={{ ($foo-1)*5 }}{{if $gimme->topic->identifier}}&tpid={{$gimme->topic->identifier}}{{/if}}">
                   {{ $foo }} </a></li>
                   {{/if}}
             {{/for}}

             {{if $lastToShow-1<$allPages}}
             <li class="firstlast">...</li>
             <li class="firstlast"><a href="?{{$list_id}}={{ ($allPages-1)*5 }}{{if $gimme->topic->identifier}}&tpid={{$gimme->topic->identifier}}{{/if}}">{{$allPages}}</a></li>
             {{/if}}

           </ul>

         {{/if}}


         {{ if $gimme->current_list->has_next_elements }}
         {{ unset_article }}
         <a href="?{{ urlparameters options="next_items" }}" class="arrow arrow_right" title="">{{'next'|translate}}</a>

         {{ /if }}
       </nav>

       {{ /if }}



       {{/if}}

      {{/list_articles}}

{{else}}


<h2 class="bigger margin_bottom_10">{{'LatestComments'|translate}}</h2>


<div class="clear">
  <div class="space_left_content">
    <ul>

      {{ list_user_comments user=$user->identifier order="bydate desc" length="30" }}
<li class="comment-content">
  <a href="{{ $gimme->user_comment->article->url }}">{{ $gimme->user_comment->article->name }}</a>

  {{ if $user->identifier }}
      {{ if $user->is_active }}
          <a href="{{ $view->url(['username' => $user->uname], 'user') }}">
          {{ strip }}
              {{ render file="_tpl/user-image.tpl" size="small" user=$gimme->comment->user }}
          {{ /strip }}
          </a>
      {{ /if }}
  {{ else }}
      <img src="{{ url static_file='_img/user-thumb-small-default.jpg' }}" alt="" />
  {{ /if }}
    <time>
    {{ $date=date_create($gimme->user_comment->submit_date) }}{{ $date->format('d.m.Y H:i') }}
    </time>
    <p>{{ $gimme->user_comment->content|escape }}</p>
</li>








      {{ /list_user_comments }}


          </ul>
        </div>
      </div>



 {{ /if }}
 {{/dynamic}}