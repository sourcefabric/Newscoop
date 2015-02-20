
{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}


<div id="page" class="container">


  <!-- Content -->
  <section id="content">
<h1>{{$gimme->topic->name}}</h1>

{{ render file="_tpl/topic_cont.tpl" params=$gimme->url->get_parameter("ls-art0") }}

    </section>
    <!-- End Content -->


  </div>


  {{ include file="_tpl/footer.tpl" }}
  {{ include file="_tpl/_html-foot.tpl" }}
