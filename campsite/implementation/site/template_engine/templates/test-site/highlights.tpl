{{ strip }}
<table class="highlights" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <div class="mainnews">
    {{ set_article number="" }}
      <a href=""><img src="" /></a>
      <div class="photocredit">
        {{ $campsite->article->image->photographer }}
      </div>
      <div class="story">
        <h4><a href="">{{ $campsite->article->name }}</a></h4>
        <div class="byline">
          {{ $campsite->article->byline }}
        </div>
        <div class="summary">
          {{ $campsite->article->intro|truncate:150 }}
        </div>
      </div>
    {{ unset_article }}
    </div>
  </td>
</tr>
<tr>
  <td>
    {{ list_articles name="highlights" length="8" columns="1" }}
      <div class="news">
        <h3><a href="">{{ $campsite->article->name }}</a></h3>
        <div class="story">
          <div class="byline">
            {{ $campsite->article->byline }}
          </div>
          <div class="summary">
            {{ $campsite->article->intro|truncate:150 }}
          </div>
        </div>
      </div>
    {{ /list_articles }}
  </td>
</tr>
</table>
{{ /strip }}