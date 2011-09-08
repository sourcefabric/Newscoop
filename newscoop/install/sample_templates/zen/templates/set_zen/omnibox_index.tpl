<input type="button" id="ob_file_upload" value="upload file"><span id="ob_file_info"></span><br>
<div id="ob_wrapper" style="position: fixed; left: 0px; top: 40%;">
<script type="text/javascript" src="{{ $view->baseUrl('/js/plupload/js/plupload.full.js') }}"></script>
<div id="ob_main" style="display: none;">
<div id="ob_message_wrapper">
	<div id="ob_message" style="display: none;"></div>
	<div id="ob_message_close" style="display: none;"><a href="javascript:omnibox.hideMessage();omnibox.setMessage('');">close</a></div>
</div>
{{ if $gimme->user->logged_in }}
	welcome {{ $gimme->user->name }}, introduction here<br>
	<a href="#" onClick="omnibox.logout();">logout</a><br>
	
	<div style="display: inline;" id="ob_input">
	
	{{ if $gimme->article->number }}
		on article page<br>
		<div>
			<input type="radio" name="ob_comment_feedback" id="ob_comment" onClick="omnibox.switchCommentFeedback();" checked="checked">
			<label for="ob_comment" style="float: none;">comment on article</label>
		</div>
		
		<div>
			<input type="radio" name="ob_comment_feedback" id="ob_feedback" onClick="omnibox.switchCommentFeedback();">
			<label for="ob_feedback" style="float: none;">feedback</label>
		</div>
		
		<div id="ob_comment_text_container" style="display: inline;">
			<label for="ob_comment_subject" style="float: none;">subject</label>
			<input type="text" id="ob_comment_subject" value=""><br>
			<label for="ob_comment_text" style="float: none;">comment</label>
			<textarea name="ob_comment_text" id="ob_comment_text"></textarea><br>
			<input type="button" value="send" onClick="omnibox.sendComment();">
		</div>
		
		<div id="ob_feedback_text_container" style="display: none;">
			<label for="ob_feedback_subject" style="float: none;">subject</label>
			<input type="text" id="ob_feedback_subject" value=""><br>
			<label for="ob_feedback_text" style="float: none;">message</label>
			<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
			<!--<input type="button" id="file_upload" value="upload file"><span id="file_info"></span><br>-->
			<input type="button" value="send" onClick="omnibox.sendFeedback();">
		</div>
	{{ else }}
		not on article page<br>
		<div id="ob_feedback_text_container">
			<label for="ob_feedback_subject" style="float: none;">subject</label>
			<input type="text" id="ob_feedback_subject" value=""><br>
			<label for="ob_feedback_text" style="float: none;">message</label>
			<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
			<!--<input type="button" id="file_upload" value="upload file"><span id="file_info"></span><br>-->
			<input type="button" value="send" onClick="omnibox.sendFeedback();">
		</div>
	{{ /if }}
	
	</div>
	
{{ else }}
	not logged in...<br>
	<input type="text" id="ob_username"><br>
	<input type="password" id="ob_password"><br>
	<input type="button" value="login" onClick="omnibox.login();"><br>
	
	<a href="{{ $view->baseUrl('/register') }}">register</a><br>
{{ /if }}
</div>

<div id="ob_handle" style="">
<a href="javascript:omnibox.showHide();">omnibox</a>
</div>

</div>
<script type="text/javascript" src="{{ $view->baseUrl('/js/omnibox.js') }}"></script>
<script>
omnibox.initialize();
{{ if $gimme->article->number }}
	omnibox.setType('comment');
{{ else }}
	omnibox.setType('feedback');
{{ /if }}
omnibox.setUploadUrl('{{ $view->baseUrl("/feedback/upload/?format=json") }}');
omnibox.setFlashRuntime('{{ $view->baseUrl('/js/plupload/js/plupload.flash.swf') }}');
omnibox.setSilverlightRuntime('{{ $view->baseUrl('/js/plupload/js/plupload.silverlight.xap') }}');
omnibox.setBaseUrl('{{ $view->baseUrl() }}');
omnibox.setLanguage('{{ $gimme->language->number }}');
omnibox.setPublication('{{ $gimme->publication->identifier }}');
omnibox.setSection('{{ $gimme->section->id }}');
omnibox.setArticle('{{ $gimme->article->number }}');
</script>