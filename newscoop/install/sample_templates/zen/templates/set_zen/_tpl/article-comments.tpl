{{ if $gimme->article->comments_enabled && $gimme->article->content_accessible }}
<div class="block" id="comments_wrap">
<h3>{{ $gimme->article->comment_count }} Response(s) to &#8220;{{ $gimme->article->name }}&#8221;</h3>
</div>
{{ list_article_comments columns="2" recommended="true" order="bydate desc"}}

<div class="block" id="comments_wrap">
{{ if $gimme->current_list->at_beginning }}
  <ol class="commentlist">
{{ /if }}
  
   <li class="comment recommended">
      <div class="comment-head">
         <div class="user-meta">
{{* get gravatar image *}}
{{ assign var="profile_email" value=$gimme->comment->reader_email }}
<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($profile_email))) }}?s=60" class="commentimage" width="60" height="60" />
             <span class="name">{{ $gimme->comment->nickname }}</span> {{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y at %H:%i" }}
          </div>
      </div>
      <div class="comment-entry">{{ $gimme->comment->content }}</div>
   </li>
 
{{ if $gimme->current_list->at_end }}
  </ol>    

{{ /if }}
</div> <!-- comments_wrap -->

{{ /list_article_comments }}

{{ list_article_comments columns="2" recommended="false" order="bydate desc"}}

<div class="block" id="comments_wrap">
   <li class="comment {{ if $gimme->current_list->column == "1" }}odd{{ else }}even{{ /if }}">
      <div class="comment-head">
         <div class="user-meta">
{{* get gravatar image *}}
{{ assign var="profile_email" value=$gimme->comment->reader_email }}
<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($profile_email))) }}?s=60" class="commentimage" width="60" height="60" />
             <span class="name">{{ $gimme->comment->nickname }}</span> {{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y at %H:%i" }}
          </div>
      </div>
      <div class="comment-entry">{{ $gimme->comment->content }}</div>
   </li>
 
{{ if $gimme->current_list->at_end }}                 
  </ol>    

{{ /if }}
</div> <!-- comments_wrap -->

{{ /list_article_comments }}

{{ /if }}
