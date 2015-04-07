{{extends file="layout.tpl"}}

{{block content}}
<div class="article_content bloger content_text white-box">
  <div class="clearfix">






 <h3 class="normal_header">{{'hello'|translate}} {{ $name }}</h3>





<p>{{'fillYourData'|translate}}</p>



{{ if !empty($error) }}
<p style="color: #c00;"><strong>{{ $error }}</strong></p>
{{ /if }}
<div class="register_form">
{{ $form }}
</div>
</div>
</div>
{{/block}}
