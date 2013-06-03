<article id="user-content">
  <header><ul>
    {{ if $user->isAuthor() }}
    <li><a href="#articles">{{ #articles# }}</a></li>
    {{ /if }}
    <li><a href="#usercomments">{{ #comments# }}</a></li>
  </ul></header>
  
  {{ if $user->isAuthor() }}
  {{ $escapedName=str_replace(" ", "\ ", $user->author->name) }}
  {{ /if }}
  
  <div id="articles">
      {{ list_articles length="5" ignore_publication="true" ignore_issue="true" ignore_section="true" constraints="author is $escapedName type is news" order="bypublishdate desc" }}
      {{ if $gimme->current_list->at_beginning }}
      <ul class="item-list extended profil-activity article">
      {{ /if }}
      <li class="clearfix">
      <h6><a href="{{ $gimme->article->url }}" title="{{ $gimme->article->title }}">{{ $gimme->article->title }}</a></h6>
        <p class="article-info"><em>{{ #publishedOn# }} {{ $gimme->article->publish_date|camp_date_format:"%M %e, %Y" }} in <a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></em><span class="right">{{ include file="_tpl/article-icons.tpl" }}</span></p>
        {{ include file="_tpl/img/img_250x167.tpl" where="author" }}{{ $gimme->article->deck }}
      </li>
{{ if $gimme->current_list->at_end }}
    </ul>      
{{* PAGINATION *}}
{{ $pages=ceil($gimme->current_list->count/5) }}
{{ $curpage=intval($gimme->url->get_parameter($gimme->current_list_id())) }}
{{ if $pages gt 1 }}
<ul class="pagination">
    {{ if $gimme->current_list->has_previous_elements }}<li class="prev"><a href="?{{ urlparameters options="previous_items" }}">{{ #previous# }}</a></li>{{ /if }}
    {{ for $i=0 to $pages - 1 }}
        {{ $curlistid=$i*5 }}
        {{ $gimme->url->set_parameter($gimme->current_list_id(),$curlistid) }}
        {{ if $curlistid != $curpage }}
    <li><a href="?{{ urlparameters }}">{{ $i+1 }}</a></li>
        {{ else }}
    <li class="selected"><a href="?{{ urlparameters }}">{{ $i+1 }}</a></li>
        {{ $remi=$i+1 }}
        {{ /if }}
    {{ /for }}
    {{ if $gimme->current_list->has_next_elements }}<li class="next"><a href="?{{ urlparameters options="next_items" }}">{{ #next# }}</a></li>{{ /if }}
</ul>
{{ $gimme->url->set_parameter($gimme->current_list_id(),$curpage) }}
{{ /if }}

{{ /if }}       
      
      {{ /list_articles }}
</div>
  
  <div id="usercomments"><ul>
    <li><ul class="item-list extended profil-activity">
      {{ list_user_comments user=$user->identifier order="bydate desc" length="30" }}
      <li class="commentar">{{ $date=date_create($gimme->user_comment->submit_date) }}
            <time>{{ $date->format('d.m.Y \u\m H:i') }}</time>
              <h6{{* class="{{ cycle values="green-txt," }}"*}}>{{ $gimme->user_comment->subject|escape }}</6>
              «{{ $gimme->user_comment->content|escape|truncate:255:"...":true }}»  {{ #onArticle# }}: <a href="{{ $gimme->user_comment->article->url }}">{{ $gimme->user_comment->article->name }}</a>
      </li>
      {{ /list_user_comments }}
    </ul></li>
  </ul></div>
</article>
<script>
$(function() {
  // remove tab switch if list is empty
  $('#user-content header a').each(function() {
    var id = $(this).attr('href');
    if ($(id).find('.item-list li').size() == 0) { // remove empty lists
      $(this).closest('li').detach();
            $(id).detach();
    } 
  });

  // init tabs only if some tab survived
  if ($('#user-content header li').size()) {
    $('#user-content').tabs();
  } else { // remove content
    $('#user-content').detach();
  }
});
</script>
