var omnibox = {
	language: false,
	baseUrl: false,
	status: false,
	type: 'comment',
	elementList: ['ob_main', 'ob_comment', 'ob_feedback', 'ob_comment_text_container', 'ob_comment_text', 'ob_feedback_text_container', 'ob_feedback_text',
		'ob_input', 'ob_review_comment', 'ob_review_comment_text', 'ob_review_feedback', 'ob_review_feedback_text', 'ob_message', 'ob_message_close',
		'ob_review_captcha', 'ob_review_captcha_image', 'ob_review_captcha_code'],
	elements: {},
	initialize: function() {
		for (var i in this.elementList) {
			var element = this.elementList[i];
			this.elements[element] = document.getElementById(element);
		}
		
		if (this.elements.ob_comment_text) this.elements.ob_comment_text.value = '';
		if (this.elements.ob_feedback_text) this.elements.ob_feedback_text.value = '';
		if (this.elements.ob_review_captcha_code) this.elements.ob_review_captcha_code.value = '';
		
		/*
		var location = (String)(document.location);
		if (location.indexOf('#') != -1) {
			this.showHide();
		}
		*/
	},
	setType: function(type) {
		this.type = type;
	},
	setBaseUrl: function(baseUrl) {
		this.baseUrl = baseUrl;
	},
	setLanguage: function(language) {
		this.language = language;
	},
	showHideElement: function(elementName, action) {
		if (action == 'show') var display = 'inline';
		else var display = 'none';
		if (typeof(elementName) == 'object') {
			var elementList = elementName;
			for (var i in elementList) {
				var elementName = elementList[i];
				if (this.elements[elementName]) this.elements[elementName].style.display = display;
			}
		}
		else {
			if (this.elements[elementName]) this.elements[elementName].style.display = display;
		}
	},
	showHide: function() {
		if (this.status == false) {
			//this.elements.ob_main.style.display = 'inline';
			$('#ob_main').show(500);
			this.status = true;
		}
		else {
			//this.elements.ob_main.style.display = 'none';
			$('#ob_main').hide(500);
			this.status = false;
		}
	},
	switchCommentFeedback: function() {
		if (this.elements.ob_comment.checked) {
			this.type = 'comment';
		}
		else if (this.elements.ob_feedback.checked) {
			this.type = 'feedback';
		}
		
		this.showInput();
	},
	showInput: function() {
		this.showHideElement(['ob_review_comment', 'ob_review_feedback', 'ob_review_captcha'], 'hide');
		this.showHideElement('ob_input', 'show');
		
		if (this.type == 'comment') {
			this.showHideElement('ob_feedback_text_container', 'hide');
			this.showHideElement('ob_comment_text_container', 'show');
		}
		if (this.type == 'feedback') {
			this.showHideElement('ob_comment_text_container', 'hide');
			this.showHideElement('ob_feedback_text_container', 'show');
		}
	},
	showReview: function() {
		var random_temp = Math.random();
		this.elements.ob_review_captcha_image.src = this.baseUrl + '/include/captcha/image.php?x=' + random_temp;
		
		this.showHideElement('ob_input', 'hide');
		this.showHideElement('ob_review_captcha', 'show');
		
		if (this.type == 'comment') {
			this.elements.ob_review_comment_text.innerHTML = this.elements.ob_comment_text.value;
			
			this.showHideElement('ob_review_comment', 'show');
			this.showHideElement('ob_review_feedback', 'hide');
		}
		if (this.type == 'feedback') {
			this.elements.ob_review_feedback_text.innerHTML = this.elements.ob_feedback_text.value;
			
			this.showHideElement('ob_review_comment', 'hide');
			this.showHideElement('ob_review_feedback', 'show');
		}
	},
	showMessage: function() {
		this.showHideElement(['ob_message', 'ob_message_close'], 'show');
	},
	hideMessage: function() {
		this.showHideElement(['ob_message', 'ob_message_close'], 'hide');
	},
	setMessage: function(message) {
		this.elements.ob_message.innerHTML = message;
	},
	sendComment: function() {
		var data = {
			f_submit_comment: 'SUBMIT',
			f_comment_nickname: '',
			f_comment_reader_email: '',
			f_comment_content: this.elements.ob_comment_text.value,
			f_article_number: document.getElementById('ob_comment_article_number').value,
			f_comment_is_anonymous: 0,
			f_comment_subject: 'SiteComment',
			f_captcha: this.elements.ob_review_captcha_code.value,
			f_language: this.language
		};
		
		$.post(this.baseUrl + '/comment/save/?format=json', data, function(data) {
			data = $.parseJSON(data);
			console.log(data);
			
			if (data.response == 'OK') {
				var location = (String)(document.location);
				location = location.split('#');
				location = location[0];
				document.location = location + '#comments_wrap';
				document.location.reload();
			}
			else {
				omnibox.setMessage(data.response);
				omnibox.showMessage();
				omnibox.showInput();
			}
		});
		
		this.elements.ob_review_captcha_code.value = '';
	},
	sendFeedback: function() {
		var data = {
			f_feedback_url: String(document.location),
			f_feedback_content: this.elements.ob_feedback_text.value,
			f_captcha: this.elements.ob_review_captcha_code.value,
			f_language: this.language,
			f_publication_id: document.getElementById('ob_feedback_publication_id').value
		};
		
		$.post(this.baseUrl + '/feedback/save/?format=json', data, function(data) {
			data = $.parseJSON(data);
			console.log(data);
			
			omnibox.setMessage(data.response);
			omnibox.showMessage();
			omnibox.showInput();
		});
		
		this.elements.ob_review_captcha_code.value = '';
	}
};