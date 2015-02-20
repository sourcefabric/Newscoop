

{{ if $gimme->article->type_name=="page"}}

  {{ include file="page.tpl" }}


{{ elseif $gimme->article->type_name == "debate" }}

  {{ include file="article-debate.tpl" }}

{{else}}



{{ include file="_tpl/_html-head.tpl" }}

<div id="blueimp_fullscreen" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>

    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <ol class="indicator"></ol>
    <div class="caption"></div>
</div>




    {{ include file="_tpl/header.tpl" }}

<div id="page" class="container">

 <!-- Content -->
 <section id="content">

  {{ include file="_tpl/article-cont.tpl" }}


      {{ render file="_tpl/box-most_tabs.tpl"  issue=off section=off cache=600 }}


    </section>
    <!-- End Content -->

  </div>


  {{ include file="_tpl/footer.tpl" }}
  {{ include file="_tpl/_html-foot.tpl" }}

{{/if}}