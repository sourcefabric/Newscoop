

<script type="text/template" id="item-template">


     <article class="news_item">

     <% var rendition = false;
     if (item.isHighlighted()){
        rendition = item.getRendition("front_big");

     }else{
      rendition = item.getRendition("front_small");

     }

      if(rendition){

     %>

         <a href="<%= item.get('number') %>" class="thumbnail">

            <img src="//<%= rendition %>"  alt="" class="loading" />
        </a>

        <% } %>

                <div class="content content_text">

                            <h6 class="info">
                            <%  var d = new Date(item.get('published'));
                                var day = d.getDate();
                                var month = d.getMonth() + 1; //Months are zero based
                                var year = d.getFullYear();
                                var hours = d.getHours();
                                var min = d.getMinutes();
                                print( ('0'+ day).slice(-2) + "." + ('0'+month).slice(-2) + "." + year+", "+('0'+hours).slice(-2)+":"+('0'+min).slice(-2));
                            %>
                            </h6>

                    <h3 class="title"><a href="<%= item.get('number') %>"> <%= item.get('title') %></a></h3>

                    <p> <%= item.getDeck().replace(/^(.{200}[^\s]*).*/, "$1")  %></p>
                </div>



    </article>



</script>



<div class="row" id="masonry_container">

</div>
<button id="load_more">load more</button>