<link rel="stylesheet" href="{{ $view->baseUrl('/public/css/omnibox.css') }}" type="text/css" media="screen" />
<div id="ob_wrapper">
<script type="text/javascript" src="{{ $view->baseUrl('/js/plupload/js/plupload.full.js') }}"></script>
<div id="ob_main" style="display: none;">
	<div id="ob_message_wrapper" style="display: none;">
		<div id="ob_message_close" style="display: none;" class="right"><a href="javascript:omnibox.hideMessage();omnibox.setMessage('');"><img src="{{ $view->baseUrl('/public/css/img/close-button.png') }}"></a></div>
		<div id="ob_message" style="display: none;"></div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	{{ if $gimme->user->logged_in }}		
		{{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
			<div class="top_title">{{ $view->translate('My comment') }} / {{ $view->translate('Send message to the editorial team') }}</div>
			<div class="top_user">
				{{ $view->translate('You are logged in as %s', $gimme->user->name) }}<br><a href="#" onClick="omnibox.logout();">{{ $view->translate('Logout') }}</a><br>
			</div>
			<div class="clear"></div>
			
			<div style="display: inline;" id="ob_input">

			<div class="radio_container">
				<input type="radio" name="ob_comment_feedback" id="ob_comment" onClick="omnibox.switchCommentFeedback();" checked="checked">
				<label for="ob_comment" style="float: none;">{{ $view->translate('Comment on article') }}</label>
			</div>
			
			<div class="radio_container">
				<input type="radio" name="ob_comment_feedback" id="ob_feedback" onClick="omnibox.switchCommentFeedback();">
				<label for="ob_feedback" style="float: none;">{{ $view->translate('Send message to the editorial team (is not published)') }}</label>
			</div>
            
            <div class="clear"></div>
			
			<div id="ob_comment_text_container" class="text_container" style="display: inline;">
				<label for="ob_comment_subject" style="float: none;">{{ $view->translate('Subject') }}</label>
				<input type="text" id="ob_comment_subject" value="" onKeyPress="if (event.keyCode == 13) omnibox.sendComment();"><br>
				<label for="ob_comment_text" style="float: none;">{{ $view->translate('Comment') }}</label>
				<textarea name="ob_comment_text" id="ob_comment_text"></textarea><br>
				<input type="button" class="send_button" value="{{ $view->translate('Publish') }}" onClick="omnibox.sendComment();">
			</div>
			
			<div id="ob_feedback_text_container" class="text_container" style="display: none;">
				<label for="ob_feedback_subject" style="float: none;">{{ $view->translate('Subject') }}</label>
				<input type="text" id="ob_feedback_subject" value="" onKeyPress="if (event.keyCode == 13) omnibox.sendFeedback();"><br>
				<label for="ob_feedback_text" style="float: none;">{{ $view->translate('Message') }}</label>
				<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
				<span id="ob_file_upload_container"></span>
				<span id="ob_file_info"></span>
				<input type="button" class="send_button" value="{{ $view->translate('Send') }}" onClick="omnibox.sendFeedback();"><br>
				<div class="clear"></div>
				<span id="ob_file_type">{{ $view->translate('Possible formats: pictures (jpg, png, gif), documents (pdf)') }}</span>
			</div>
			<div class="clear"></div>
			
			</div>
		{{ else }}
			<div class="top_title">{{ $view->translate('Send message to the editorial team') }}</div>
			<div class="top_user">
				{{ $view->translate('Registered as') }} {{ $gimme->user->name }}<br><a href="#" onClick="omnibox.logout();">{{ $view->translate('Logout') }}</a><br>
			</div>
			<div class="clear"></div>
			
			<div style="display: inline;" id="ob_input">
			
			<div id="ob_feedback_text_container" class="text_container">
				<label for="ob_feedback_subject" style="float: none;">{{ $view->translate('Subject') }}</label>
				<input type="text" id="ob_feedback_subject" value="" onKeyPress="if (event.keyCode == 13) omnibox.sendFeedback();"><br>
				<label for="ob_feedback_text" style="float: none;">{{ $view->translate('Message') }}</label>
				<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
				<span id="ob_file_upload_container"></span>
				<span id="ob_file_info"></span>
				<input type="button" class="send_button" value="{{ $view->translate('Send') }}" onClick="omnibox.sendFeedback();"><br>
				<div class="clear"></div>
				<span id="ob_file_type">{{ $view->translate('Possible formats: pictures (jpg, png, gif), documents (pdf)') }}</span>
			</div>
			<div class="clear"></div>
			
			</div>
		{{ /if }}
		
	{{ else }}
		<div class="top_title">{{ $view->translate('Login') }}</div>
		<div class="clear"></div>
		<div class="text_container left half">
			{{ $view->translate('You have to be registered in order to comment on articles and send messages directly to the editorial team. Please login or create a free user account.') }}
			<br><a href="">{{ $view->translate('login_link_text') }}</a>
		</div>
		<div class="text_container right half">
                        <div class="login_label">{{ $view->translate('E-Mail') }}</div>
			<input type="text" id="ob_email" name="ob_email" class="small right" onKeyPress="if (event.keyCode == 13) omnibox.login();"><br>
			<div class="clear"></div>
                        <div class="login_label">{{ $view->translate('Password') }}</div>
                        <div class="clear"></div>
			<input type="password" id="ob_password" name="ob_password" class="small right" onKeyPress="if (event.keyCode == 13) omnibox.login();"><br>
			<div class="clear"></div>
			<input type="button" class="login_button right" value="{{ $view->translate('Login') }}" onClick="omnibox.login();">
			<a class="register_link right" href="{{ $view->baseUrl('/register') }}">{{ $view->translate('Register') }}</a>
            <a class="register_link right" href="{{ $view->baseUrl('/auth/password-restore ') }}">{{ $view->translate('Forgot password') }}</a>
		</div>
		
		<div class="clear"></div>
	{{ /if }}
</div>

<div id="ob_handle" style="">
<a href="javascript:omnibox.showHide();">
<img id="ob_handle_image" src="{{ $view->baseUrl('/public/css/img/green-triangle.png') }}">
</a>
</div>

</div>
<script type="text/javascript" src="{{ $view->baseUrl('/js/omnibox.js') }}"></script>
<script>
omnibox.setBaseUrl('{{ $view->baseUrl() }}');
omnibox.initialize();
{{ if $gimme->article->number && $gimme->article->comments_locked == 0 && $gimme->article->comments_enabled == 1}}
	omnibox.setType('comment');
{{ else }}
	omnibox.setType('feedback');
{{ /if }}
omnibox.setTranslation('attach_file', '{{ $view->translate("Attach file") }}');
omnibox.setTranslation('feedback_content_empty', '{{ $view->translate("Feedback content is not filled in.") }}');
omnibox.setTranslation('login_failed', '{{ $view->translate("Login failed.") }}');
omnibox.setTranslation('login_successful', '{{ $view->translate("Login successful. Please wait...") }}');
omnibox.setTranslation('logout_successful', '{{ $view->translate("Logout successful. Please wait...") }}');
omnibox.setTranslation('banned_feedback', '{{ $view->translate("You have been banned from writing feedbacks.") }}');
omnibox.setTranslation('not_logged_in', '{{ $view->translate("You are not logged in.") }}');
omnibox.setTranslation('feedback_file_sent', '{{ $view->translate("File is uploaded and your message is sent.") }}');
omnibox.setTranslation('feedback_sent', '{{ $view->translate("Your message is sent.") }}');
omnibox.setTranslation('error_header', '{{ $view->translate("Following errors have been found:") }}');
omnibox.setTranslation('banned_comment', '{{ $view->translate("You have been banned from writing comments.") }}');
omnibox.setTranslation('comment_subject_empty', '{{ $view->translate("The comment subject was not filled in.") }}');
omnibox.setTranslation('comment_content_empty', '{{ $view->translate("The comment content was not filled in.") }}');

omnibox.setUploadUrl('{{ $view->baseUrl("/feedback/upload/?format=json") }}');
omnibox.setFlashRuntime('{{ $view->baseUrl('/js/plupload/js/plupload.flash.swf') }}');
omnibox.setSilverlightRuntime('{{ $view->baseUrl('/js/plupload/js/plupload.silverlight.xap') }}');
omnibox.setLanguage('{{ $gimme->language->number }}');
omnibox.setPublication('{{ $gimme->publication->identifier }}');
omnibox.setSection('{{ $gimme->section->id }}');
omnibox.setArticle('{{ $gimme->article->number }}');
</script>
