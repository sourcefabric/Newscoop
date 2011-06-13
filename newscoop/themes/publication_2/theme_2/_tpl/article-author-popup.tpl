{{ list_article_authors }} 
<div id="hidden{{ $gimme->current_list->index }}Content" class="teammemberinfo" style="display:none">
  <div style="float: right;"><input type="submit" id="close" value="&nbsp;&nbsp;close&nbsp;&nbsp;" onclick="tb_remove()" /></div>
  <img style="width: 150px; float: left; margin: 0 10px 10px 0" src="{{ $gimme->author->picture->imageurl }}" />
  <h2>{{ $gimme->author->name }}</h2>
  <div class="text">{{ $gimme->author->biography->text }}</div>
</div>    
{{ /list_article_authors }}