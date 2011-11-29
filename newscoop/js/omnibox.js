var omnibox = {
	uploader: false,
	flashRuntime: false,
	silverlightRuntime: false,
	uploadUrl: false,
	uploadStatus: true,
	language: false,
	publication: false,
	section: false,
	article: false,
	baseUrl: '',
	status: false,
	translations: {},
	type: 'comment',
	elementList: ['ob_main', 'ob_comment', 'ob_feedback', 'ob_comment_text_container', 'ob_comment_subject', 'ob_comment_text', 'ob_feedback_text_container', 'ob_feedback_subject',
		'ob_feedback_text', 'ob_input', 'ob_message_wrapper', 'ob_message', 'ob_message_close', 'ob_file_info', 'ob_email', 'ob_password', 'ob_file_upload_container', 'ob_handle_image'],
	elements: {},
	initialize: function() {
		for (var i in this.elementList) {
			var element = this.elementList[i];
			this.elements[element] = document.getElementById(element);
		}
		
		if (this.elements.ob_comment_subject) this.elements.ob_comment_subject.value = '';
		if (this.elements.ob_comment_text) this.elements.ob_comment_text.value = '';
		if (this.elements.ob_feedback_subject) this.elements.ob_feedback_subject.value = '';
		if (this.elements.ob_feedback_text) this.elements.ob_feedback_text.value = '';
		
		if (document.location.hash == '#omnibox') {
			this.showHide();
		}
	},
	setType: function(type) {
		this.type = type;
	},
	setTranslation: function(key, value) {
		this.translations[key] = value;
	},
	setFlashRuntime: function(flashRuntime) {
		this.flashRuntime = flashRuntime;
	},
	setSilverlightRuntime: function(silverlightRuntime) {
		this.silverlightRuntime = silverlightRuntime;
	},
	setUploadUrl: function(uploadUrl) {
		this.uploadUrl = uploadUrl;
	},
	setBaseUrl: function(baseUrl) {
		this.baseUrl = baseUrl;
	},
	setLanguage: function(language) {
		this.language = language;
	},
	setSection: function(section) {
		this.section = section;
	},
	setPublication: function(publication) {
		this.publication = publication;
	},
	setArticle: function(article) {
		this.article = article;
	},
	showUploader: function() {
		this.uploader = new plupload.Uploader({
			runtimes: 'html5,html4',
			browse_button: 'ob_file_upload',
			max_file_size: '20mb',
			flash_swf_url: this.flashRuntime,
			silverlight_xap_url: this.silverlightRuntime,
			url: this.uploadUrl,
			filters: [{title: "Image files", extensions: "jpg,gif,png"}, {title: "Document", extensions: "pdf"}],
			multi_selection: false
		});
		this.uploader.init();
		this.uploader.refresh();
		
		var that = this;
		this.uploader.bind('FilesAdded', function(up, files) {
			if (this.files.length > 1) {
				this.removeFile(this.files[0]);
			}
			that.elements.ob_file_info.innerHTML = files[0].name;
		});
		this.uploader.bind('FileUploaded', function(up, file, info) {
			var fileNameParts = file.name.split('.');
			var extension = fileNameParts[fileNameParts.length - 1];
			extension = extension.toLowerCase();
			
			if (extension == 'jpg' || extension == 'gif' || extension == 'png') {
				response = $.parseJSON(info.response);
				response = response.response;
				that.sendFeedback('image', response);
			}
			if (extension == 'pdf') {
				response = $.parseJSON(info.response);
				response = response.response;
				that.sendFeedback('document', response);
			}
		});
	},
	hideUploader: function() {
		this.uploader.refresh();
	},
	showHideElement: function(elementName, action) {
		if (action == 'show') var display = 'inline';
		else var display = 'none';
		
		if (typeof(elementName) == 'object') {
			var elementList = elementName;
			for (var i in elementList) {
				var elementName = elementList[i];
				if (this.elements[elementName]) {
					this.elements[elementName].style.display = display;
					if (display == 'inline' && elementName == 'ob_message_wrapper') {
						this.elements[elementName].style.display = 'block';
					}
				}
			}
		}
		else {
			if (this.elements[elementName]) this.elements[elementName].style.display = display;
		}
	},
	showHide: function() {
		if (this.status == false) {
			//this.elements.ob_main.style.display = 'inline';
			$('#ob_wrapper').css('width', '690px');
            $('#ob_main').show(400);
            this.status = true;
			if (this.elements.ob_file_upload_container) this.elements.ob_file_upload_container.innerHTML = '<input type="button" id="ob_file_upload" value="'+this.translations['attach_file']+'">';
            this.elements.ob_handle_image.src = this.baseUrl + '/public/css/img/green-triangle-close.png';
            setTimeout('omnibox.showUploader();', 200);
		}
		else {
			//this.elements.ob_main.style.display = 'none';
			$('#ob_wrapper').css('width', '0px');
            $('#ob_main').hide(400);
			this.status = false;
			this.hideUploader();
            this.elements.ob_handle_image.src = this.baseUrl + '/public/css/img/green-triangle.png';
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
	login: function() {
		var data = {
			email: this.elements.ob_email.value,
			password: this.elements.ob_password.value
		};
		
		this.elements.ob_email.value = '';
		this.elements.ob_password.value = '';
		
		$.post(this.baseUrl + '/omnibox/login/?format=json', data, function(data) {
			data = $.parseJSON(data);
			
			if (data.response == 'OK') {
				omnibox.setMessage(omnibox.translations['login_successful']);
				omnibox.showMessage();
				document.location.hash = '#omnibox';
                document.location.reload();
			}
			else {
				omnibox.setMessage(data.response);
				omnibox.showMessage();
			}
		});
	},
	logout: function() {
		$.post(this.baseUrl + '/omnibox/logout/?format=json', {}, function(data) {
			omnibox.setMessage(omnibox.translations['logout_successful']);
			omnibox.showMessage();
			document.location.reload();
		});
	},
	showMessage: function() {
		this.showHideElement(['ob_message_wrapper', 'ob_message', 'ob_message_close'], 'show');
	},
	hideMessage: function() {
		this.showHideElement(['ob_message_wrapper', 'ob_message', 'ob_message_close'], 'hide');
	},
	setMessage: function(message) {
		this.elements.ob_message.innerHTML = message;
	},
	showInput: function() {
		this.showHideElement('ob_input', 'show');
		
		if (this.type == 'comment') {
			this.showHideElement('ob_feedback_text_container', 'hide');
			this.showHideElement('ob_comment_text_container', 'show');
		}
		if (this.type == 'feedback') {
			this.showHideElement('ob_comment_text_container', 'hide');
			this.showHideElement('ob_feedback_text_container', 'show');
			if (this.elements.ob_file_upload_container) this.elements.ob_file_upload_container.innerHTML = '<input type="button" id="ob_file_upload" value="'+this.translations['attach_file']+'">';
			setTimeout('omnibox.showUploader();', 200);
		}
	},
	sendComment: function() {
		var data = {
			f_submit_comment: 'SUBMIT',
			f_comment_nickname: '',
			f_comment_reader_email: '',
			f_comment_content: this.elements.ob_comment_text.value,
			f_article_number: this.article,
			f_comment_is_anonymous: 0,
			f_comment_subject: this.elements.ob_comment_subject.value,
			f_language: this.language
		};
		
		$.post(this.baseUrl + '/comment/save/?format=json', data, function(data) {
			data = $.parseJSON(data);
			
			if (data.response == 'OK') {
				var location = (String)(document.location);
				location = location.split('#');
				location = location[0];
				document.location = location + '#tab-2';
				document.location.reload();
			}
			else {
				omnibox.setMessage(data.response);
				omnibox.showMessage();
			}
		});
	},
	sendFeedback: function(fileType, fileId) {
		if (this.elements.ob_feedback_subject.value == '' || this.elements.ob_feedback_text.value == '') {
			omnibox.setMessage(this.translations['feedback_content_empty']);
			omnibox.showMessage();
		}
		else {
			var that = this;
			if (this.uploader.total.queued > 0) {
				this.uploader.start();
			}
			else {
				var data = {
					f_feedback_url: String(document.location),
					f_feedback_subject: this.elements.ob_feedback_subject.value,
					f_feedback_content: this.elements.ob_feedback_text.value,
					f_language: this.language,
					f_section: this.section,
					f_article: this.article,
					f_publication: this.publication
				};
				
				if (fileType == 'image') {
					data['image_id'] = fileId;
				}
				if (fileType == 'document') {
					data['document_id'] = fileId;
				}
				
				var url = this.baseUrl + '/feedback/save/?format=json';
				$.post(url, data, function(data) {
					data = $.parseJSON(data);
					
					omnibox.setMessage(data.response);
					omnibox.showMessage();
					omnibox.showUploader();
				});
				
				this.elements.ob_feedback_subject.value = '';
				this.elements.ob_feedback_text.value = '';
				this.elements.ob_file_info.innerHTML = '';
			}

		}
	}
};
