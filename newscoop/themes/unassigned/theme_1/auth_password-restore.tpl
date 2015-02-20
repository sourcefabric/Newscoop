{{extends file="layout.tpl"}}
{{block content}}
<div class="article_content bloger content_text white-box">
  <div class="clearfix">
 <h3 class="normal_header">{{'resetPassword'|translate}}</h3>


<div class="register_form">
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <div class="form_level">
        {{ if $form->email->hasErrors() }}
        <div class="alert alert-error">

 <h5> {{'emailNotCorrect'|translate}}</h5>
            <p> {{'maybeYouRegistered'|translate}} <em>{{ $gimme->publication->name }}</em>  {{'withAnotherEmail'|translate}}</p>




        </div>
        {{ /if }}
   </div>
    <div class="form_level">
        {{ $form->email->setLabel("{{'email'|translate}}")->removeDecorator('Errors') }}
    </div>
         <div class="form_level margin_top_10">
                                <button type="submit" id="submit" class="purple_button "> {{'requestNewPassword'|translate}}</button>
                            </div>



</form>
</div>
</div>
</div>
{{/block}}
