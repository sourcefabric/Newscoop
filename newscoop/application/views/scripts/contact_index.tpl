<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ url static_file="assets/css/main.css" }}">
    <link rel="stylesheet" href="{{ url static_file="assets/css/skin.css" }}">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script>window.jQuery || document.write("<script src='{{ url static_file="assets/js/libs/jquery.min.js" }}'>\x3C/script>")</script>
    <script src="{{ uri static_file="assets/js/libs/jquery.dropdownized.min.js" }}"></script>
</head>
<body>
    <div id="kontakt-form" class="popup-form">
        <div class="popup-form">
        <h4>Kontakt</h4>
        {{ if isset($success) }}
            <h2>Vielen Dank für Ihre Nachricht an zentral+.</h2>
        {{ else }}
        <form method="POST" id="kontakt-form-form" action="/contact/index" style="min-width:400px;">
            <fieldset>
                <ul>
                    <li>
                        <select name="topic" class="dropdownized-fancy-1">
                            <option {{ if $smarty.post.topic == "Nachricht an die Redaktion" }}selected="selected"{{ /if }} value="Nachricht an die Redaktion">Nachricht an die Redaktion</option>
                            <option {{ if $smarty.post.topic == "Nachricht für den Verlag" }}selected="selected"{{ /if }} value="Nachricht für den Verlag">Nachricht für den Verlag</option>
                            <option {{ if $smarty.post.topic == "Bitte um Kontaktaufnahme" }}selected="selected"{{ /if }} value="Bitte um Kontaktaufnahme">Bitte um Kontaktaufnahme</option>
                            <option {{ if $smarty.post.topic == "Technische Hinweise" }}selected="selected"{{ /if }} value="Technische Hinweise">Technische Hinweise</option>
                            <option {{ if $smarty.post.topic == "Passwortprobleme" }}selected="selected"{{ /if }} value="Passwortprobleme">Passwortprobleme</option>
                            <option {{ if $smarty.post.topic == "Anderes Thema" }}selected="selected"{{ /if }} value="Anderes Thema">Anderes Thema</option>
                        </select>
                    </li>
                    <li>
                        <label>Email<i>*</i></label>
                        <input type="text" id="contact-email" value="{{ $smarty.post.contact_email }}" name="contact_email" />
                    </li>
                    <li>
                        <label>Betreff</label>
                        <input type="text" name="subject" value="{{ $smarty.post.subject }}" />
                    </li>
                    <li>
                        <label>Mitteilung<i>*</i></label>
                        <textarea id="contact-message" style="min-width: 350px; min-height: 130px;" name="contact_message">{{ $smarty.post.contact_message }}</textarea>
                    </li>
                    <li>
                        {{ if isset($errors) }}
                        <ul>
                            {{ foreach from=$errors item=error  }}
                                <li style="color: rgb(180, 0, 50)">{{ $error }}</li>
                            {{ /foreach }}
                        </ul>
                        {{ /if }}

                        {{ recaptcha }}
                    </li>
                    <li class="top-line">
                        <input type="hidden" name="publicationId" value="{{ $gimme->publication->identifier }}" />
                        <input type="submit" class="button red right" value="Senden" />
                    </li>
                </ul>
            </fieldset>
        </form>
        {{ /if }}
        </div>
    </div>
    <script type="text/javascript">
    $(".dropdownized-fancy-1").dropdownized(); 

    $(document).ready(function(){
        $('form#kontakt-form-form').submit(function(e){
            var form = this;

            if ($('#contact-email', form).val() === '') {
                alert('Email ist ein Pflichtfeld');
                return false;
            } else if ($('#contact-message', form).val() === '') {
                alert('Nachricht ist ein Pflichtfeld');
                return false;
            }  
        });
    });
    </script>
</body>
</html>