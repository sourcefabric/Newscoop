
{{ include file="_tpl/_html-head.tpl" }}

    {{ include file="_tpl/header.tpl" }}


<div id="page" class="container">

  <!-- Content -->
  <section id="content">



   <div class="row article_content">
       <div class="columnist span12">

           <div class="content content_text">

               <h2 class="name">{{$gimme->article->name}}</h2>
               {{$gimme->article->full_text}}
           </div>

       </div>
   </div>


       </section>
       <!-- End Content -->


</div>


     {{ include file="_tpl/footer.tpl" }}
     {{ include file="_tpl/_html-foot.tpl" }}
