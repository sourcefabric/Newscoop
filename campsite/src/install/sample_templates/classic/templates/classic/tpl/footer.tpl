<div id="footer"><div class="block-label" id="block-title-mostpopular">Most popular:</div>
<div id="block-articles-mostpopular">
<div id="block-articles-mostpopularinner">
{{ list_articles length="5" order="byPopularity desc" ignore_section="true" }}
<div id="block-articles-mostpopular-item" class="block-articles-mostpopular-item-{{ $gimme->section->number }}">
<div id="block-articles-mostpopular-iteminner" class="block-articles-mostpopular-iteminner-{{ $gimme->section->number }}">
<a href="{{ uri }}" class="block-mostpopular-link">
<div id="block-mostpopular-section" class="block-mostpopular-section-{{ $gimme->section->number }}">{{ $gimme->section->name }}</div>
<div id="block-mostpopular-title">{{ $gimme->article->name }}</div>
<div id="block-mostpopular-deck">{{ $gimme->article->deck }}</div>
<!--div class="block-mostpopular-link" id="block-mostpopular-counter">({{ $gimme->article->reads }} reads)</div-->
</a>
</div><!-- id="block-articles-mostpopular-iteminner" -->
</div><!-- id="block-articles-mostpopular-item" -->
{{ /list_articles }}
</div><!-- id="block-articles-mostpopularinner" -->
</div><!-- id="block-articles-mostpopular" -->
<div id="footerlinks">
<div id="footerlinksinner">
<center>
          <a href="{{ uri options="issue" }}">{{ if $gimme->language->name == "English" }}Home{{ else }}Portada{{ /if }}</a>
      {{ list_sections name="sections" constraints="number greater_equal 10 number smaller_equal 120" }}
          <a href="{{ uri }}">{{ $gimme->section->name }}</a>
      {{ /list_sections }}
</center>
</div><!-- id="footerlinksinner" -->
</div><!-- id="footerlinksinner" -->
<div style="text-align: center; margin-bottom: 10px">
      <A HREF="http://campsite.sourcefabric.org"><IMG SRC="http://www.sourcefabric.org/fs/poweredby/Newscoop_SF_Logo_M_RGB.png" height="31" width="88" align="Center" border="0"></a>
</div>
</div>
