    <a name="comments" id="comments"></a>

{{ if $gimme->article->comments_enabled }}

{{ list_article_comments order="bydate desc"}}

{{ if $gimme->current_list->at_beginning }}
    <h3 class="reply">{{ $gimme->article->comment_count }} {{ if $gimme->language->english_name == "English" }}Response(s) to{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Respuesta(s) a{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Odpowiedz(i) do{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Ответ на{{ /if }} &#8220;{{ $gimme->article->name }}&#8221;</h3>
    <ol class="commentlist">
{{ /if }}

        <li id="comment-{{ $gimme->article->number }}-{{ $gimme->current_list->index }}" class="byuser bypostauthor">
          <div class="comment_text"><p>{{ $gimme->comment->content }}</p></div>
  
          <div class="comment_author vcard">
            {{* gravatar <img alt='' src="/templates/_temp/ava2.jpeg" class='avatar avatar-32 photo' height='32' width='32' />*}}
            <p><strong class="fn">{{ if !($gimme->comment->subject == "") }}<a href="{{ $gimme->comment->subject }}" target="_blank">{{ $gimme->comment->nickname }}</a>{{ else }}{{ $gimme->comment->nickname }}{{ /if }}</strong></p>
            <p><small>{{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y @ %H:%i" }}</small></p>
          </div>
          <div class="clear"></div>
        </li>
        
{{ if $gimme->current_list->at_end }}                 
  </ol>    
{{ /if }}

{{ /list_article_comments }}

    <a name="respond" id="respond"></a>
    
{{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
    {{ if $gimme->language->english_name == "English" }}<div class="posterror">Your comment has not been accepted.</div>{{ /if }}
    {{ if $gimme->language->english_name == "Spanish" }}<div class="posterror">Tu comentario no fue aceptado.</div>{{ /if }}
    {{ if $gimme->language->english_name == "Polish" }}<div class="posterror">Komentarz nie został zaakceptowany.</div>{{ /if }}
    {{ if $gimme->language->english_name == "Russian" }}<div class="posterror">Ваш комментарий не принят к модерации.</div>{{ /if }}
{{ /if }}

{{ if $gimme->submit_comment_action->is_error }}
    <div class="posterror">{{ $gimme->submit_comment_action->error_message }}
        <span class="posterrorcode">{{ $gimme->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            {{ if $gimme->language->english_name == "English" }}<div class="postinformation">Your comment has been sent for approval.</div>{{ /if }}
            {{ if $gimme->language->english_name == "Spanish" }}<div class="postinformation">Tu comentario se envió a aprobación.</div>{{ /if }}
            {{ if $gimme->language->english_name == "Polish" }}<div class="postinformation">Komentarz został wysłany do akceptacji.</div>{{ /if }}
            {{ if $gimme->language->english_name == "Russian" }}<div class="postinformation">Ваш комментарий отправлен на модерацию.</div>{{ /if }}
        {{ /if }}
    {{ /if }}   
{{ /if }}

{{* if $gimme->comment->defined }}
    <p><strong>{{ $gimme->comment->subject }}
        ({{ $gimme->comment->reader_email|obfuscate_email }}) -
        {{ $gimme->comment->level }}</strong></p>
    <p>{{  $gimme->comment->content }}</p>
{{ /if *}}   
    
    {{ if $gimme->language->english_name == "English" }}<h3 class="reply">Leave a Reply</h3>{{ /if }}
    {{ if $gimme->language->english_name == "Spanish" }}<h3 class="reply">Deja una respuesta</h3>{{ /if }}
    {{ if $gimme->language->english_name == "Polish" }}<h3 class="reply">Zostaw odpowiedź</h3>{{ /if }}
    {{ if $gimme->language->english_name == "Russian" }}<h3 class="reply">Отправить ответ</h3>{{ /if }}
    <div class="postinput">    
    {{ comment_form html_code="id=\"commentform\"" submit_button="Submit Comment" button_html_code="id=\"submit\" tabindex=\"6\"" }}    
        <p>{{ camp_edit object="comment" attribute="nickname" html_code="class=\"comment\" id=\"author\" size=\"22\" tabindex=\"1\"" }}
        {{ if $gimme->language->english_name == "English" }}<label for="author"><small>Name (required)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<label for="author"><small>Nombre (requerido)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<label for="author"><small>Imię (wymagane)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<label for="author"><small>Имя (обязательно)</small></label></p>{{ /if }}
        
        <p>{{ camp_edit object="comment" attribute="reader_email" html_code="class=\"comment\" id=\"email\" size=\"22\" tabindex=\"2\"" }}
        {{ if $gimme->language->english_name == "English" }}<label for="email"><small>E-mail (will not be published) (required)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<label for="email"><small>Correo electrónico (no se publicará) (requerido)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<label for="email"><small>E-mail (nie zostanie opublikowany) (wymagane)</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<label for="email"><small>Mail (не будет виден другим пользователям) (обязательно)</small></label></p>{{ /if }}
        
        <p>{{ camp_edit object="comment" attribute="subject" html_code="class=\"comment\"  id=\"url\" size=\"22\" tabindex=\"3\"" }}
        {{ if $gimme->language->english_name == "English" }}<label for="url"><small>Website</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<label for="url"><small>Sitio web</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<label for="url"><small>Twoja strona</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<label for="url"><small>Вебсайт</small></label></p>{{ /if }}

        <p>{{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" cols=\"100%\" rows=\"10\" tabindex=\"4\"" }}</p>

        <p><img src="{{ captcha_image_link }}"><br />
        {{ camp_edit object="captcha" attribute="code" html_code="id=\"comment-code\" tabindex=\"5\"" }}
        {{ if $gimme->language->english_name == "English" }}<label for="f_captcha_code"><small>Enter the code</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Spanish" }}<label for="f_captcha_code"><small>Ingresa código</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Polish" }}<label for="f_captcha_code"><small>Podaj kod</small></label></p>{{ /if }}
        {{ if $gimme->language->english_name == "Russian" }}<label for="f_captcha_code"><small>Введите код</small></label></p>{{ /if }}

        <p>{{ /comment_form }}</p>
    </div>
    
{{ else }}
  {{ if $gimme->language->english_name == "English" }}<p>Comments are locked for this post</p>{{ /if }}
  {{ if $gimme->language->english_name == "Spanish" }}<p>No es posible comentar este artículo</p>{{ /if }}
  {{ if $gimme->language->english_name == "Polish" }}<p>Komentarze dla tego wpisu są zablokowane</p>{{ /if }}
  {{ if $gimme->language->english_name == "Russian" }}<p>Для этой записи комментарии заблокированы.</p>{{ /if }}
{{ /if }}    