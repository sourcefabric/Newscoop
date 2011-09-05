<div id="ob_wrapper" style="position: fixed; left: 0px; top: 40%;">
<div id="ob_main" style="display: none;">
{{ if $gimme->user->logged_in }}
	welcome {{ $gimme->user->name }}, introduction here<br>
	
	<div style="display: inline;" id="ob_input">
	
	<div id="ob_message_wrapper">
		<div id="ob_message" style="display: none;"></div>
		<div id="ob_message_close" style="display: none;"><a href="javascript:omnibox.hideMessage();omnibox.setMessage('');">close</a></div>
	</div>
	
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
			<input type="button" value="next" onClick="omnibox.showReview();">
		</div>
		
		<div id="ob_feedback_text_container" style="display: none;">
			<label for="ob_feedback_subject" style="float: none;">subject</label>
			<input type="text" id="ob_feedback_subject" value=""><br>
			<label for="ob_feedback_text" style="float: none;">message</label>
			<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
			-- here comes file upload<br>
			<input type="button" value="next" onClick="omnibox.showReview();">
		</div>
	{{ else }}
		not on article page<br>
		<div id="ob_feedback_text_container">
			<label for="ob_feedback_subject" style="float: none;">subject</label>
			<input type="text" id="ob_feedback_subject" value=""><br>
			<label for="ob_feedback_text" style="float: none;">message</label>
			<textarea name="ob_feedback_text" id="ob_feedback_text"></textarea><br>
			-- here comes file upload<br>
			<input type="button" value="next" onClick="omnibox.showReview();">
		</div>
	{{ /if }}
	
	</div>
	
	<div style="display: none;" id="ob_review_captcha">
		{{ if $gimme->publication->captcha_enabled }}
			<img id="ob_review_captcha_image" src=""><br>
			<input type="text" id="ob_review_captcha_code"><br>
		{{ else }}
			<img id="ob_review_captcha_image" src="" style="display: none;"><br>
			<input type="hidden" id="ob_review_captcha_code" value="">
		{{ /if }}
	</div>
	
	<div style="display: none;" id="ob_review_comment">
		comments are not moderated, so be nice, etc.<br>
		<div style="border: 1px solid #000000;" id="ob_review_comment_subject"></div><br>
		<div style="border: 1px solid #000000;" id="ob_review_comment_text"></div><br>
		<input type="button" value="back" onClick="omnibox.showInput();">
		<input type="button" value="send" onClick="omnibox.sendComment();">
		<br>
	</div>
	<div style="display: none;" id="ob_review_feedback">
		this is review part...
		<div style="border: 1px solid #000000;" id="ob_review_feedback_subject"></div><br>
		<div style="border: 1px solid #000000;" id="ob_review_feedback_text"></div><br>
		<input type="button" value="back" onClick="omnibox.showInput();">
		<input type="button" value="send" onClick="omnibox.sendFeedback();">
		<br>
	</div>
	
{{ else }}
	not logged in...
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
omnibox.setBaseUrl('{{ $view->baseUrl() }}');
omnibox.setLanguage('{{ $gimme->language->number }}');
omnibox.setPublication('{{ $gimme->publication->identifier }}');
omnibox.setSection('{{ $gimme->section->id }}');
omnibox.setArticle('{{ $gimme->article->number }}');
</script>