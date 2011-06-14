editAreaLoader.load_syntax["smarty"] = {
	'COMMENT_MULTI' : {'{{*' : '*}}'}
	,'QUOTEMARKS' : {1: "'", 2: '"'}
	,'KEYWORD_CASE_SENSITIVE' : true
	,'KEYWORDS' : {
		'constants' : [
			// Campsite smarty functions and Smarty custom functions
			'comment_form', 'list_article_attachments', 'list_article_audio_attachments', 'list_article_comments',
			'list_article_images', 'list_articles', 'list_article_topics', 'list_issues', 'list_languages',
			'list_search_results', 'list_sections', 'list_subtitles', 'list_subtopics', 'local', 'login_form',
			'search_form', 'subscription_form', 'user_form', 'breadcrumb', 'calendar', 'camp_edit', 'camp_select',
			'captcha_image_link', 'disable_html_encoding', 'enable_html_encoding', 'formparameters', 'set_article',
			'set_current_issue', 'set_default_article', 'set_default_issue', 'set_default_language', 'set_default_publication',
			'set_default_section', 'set_default_topic', 'set_issue', 'set_language', 'set_publication', 'set_section',
			'set_topic', 'unset_article', 'unset_comment', 'unset_issue', 'unset_language', 'unset_publication',
			'unset_section', 'unset_topic', 'uripath', 'uri', 'urlparameters', 'url', 'camp_date_format', 'camp_filesize_format',
			'obfuscate_email', 'teaser',
			'assign', 'counter', 'cycle', 'debug', 'eval', 'fetch', 'html_checkboxes', 'html_image', 'html_options', 'html_radios',
                        'html_select_date', 'html_select_time', 'html_table', 'mailto', 'math', 'popup', 'popup_init', 'textformat',
                        'echo', 'print', 'global', 'static', 'exit', 'array', 'empty', 'eval', 'isset', 'unset', 'die',
			'capitalize', 'cat', 'count_characters', 'count_paragraphs', 'count_sentences', 'count_words', 'date_format',
			'escape', 'indent', 'lower', 'nl2br', 'regex_replace', 'replace', 'spacify', 'string_format', 'strip',
			'strip_tags', 'truncate', 'upper', 'wordwrap'
		]
		,'statements' : [
			// Smarty built-in functions
			'capture', 'config_load', 'foreach', 'foreachelse',
			'if', 'elseif', 'else', 'include', 'include_php', 'insert',
			'ldelim', 'rdelim', 'literal', 'php', 'section', 'sectionelse', 'strip'
		]
		,'reserved' : [
			// 
			'name', 'number', 'english_name', 'code', 'defined',
			'identifier', 'site', 'defined', 'public_comments', 'moderated_comments', 'captcha_enabled',
			'subscription_currency', 'subscription_time_unit', 'subscription_trial_time',
			'subscription_paid_time', 'subscription_time', 'subscription_unit_cost',
			'subscription_unit_cost_all_lang', 'date', 'publish_date', 'url_name', 'is_current',
			'description', 'author', 'keywords', 'has_keyword', 'type_name', 'creation_date',
			'last_update', 'translated_to', 'subtitles_count', 'subtitle_url_id', 'current_subtitle_no',
			'owner', 'comments_enabled', 'comments_locked', 'comment_count', 'on_front_page',
			'on_section_page', 'is_published', 'is_public', 'is_indexed', 'content_accessible',
			'has_attachments', 'image_index', 'has_image', 'topics_count', 'has_topics', 'has_topic',
			'reads', 'request_object_id', 'all_subtitles', 'first_paragraph', 'subtitle_number',
			'subtitle_is_current', 'has_previous_subtitles', 'has_next_subtitles', 'file_name',
			'mime_type', 'extension', 'size_b', 'size_kb', 'size_mb', 'title', 'creator', 'genre',
			'length', 'year', 'bitrate', 'samplerate', 'album', 'format', 'composer', 'channels',
			'rating', 'track_no', 'disk_no', 'lyrics', 'real_name', 'nickname', 'reader_email',
			'submit_date', 'subject', 'content', 'level', 'field_name', 'formatted_name', 'count',
			'photographer', 'place', 'article_index', 'imageurl', 'thumbnailurl', 'uname', 'gender',
			'email', 'city', 'str_address', 'state', 'phone', 'fax', 'country', 'country_code',
			'second_phone', 'postal_code', 'field1', 'field2', 'field3', 'field4', 'field5',
			'text1', 'text2', 'text3', 'pref1', 'pref2', 'pref3', 'pref4', 'logged_in', 'blocked_from_comments',
			'is_admin', 'has_permission', 'start_date', 'expiration_date', 'is_active', 'is_valid',
			'has_section', 'at_beginning', 'at_end', 'has_next_elements', 'has_previous_elements',
			'previous_start', 'next_start', 'current', 'end', 'index', 'row', 'is_error', 'error_code',
			'error_message', 'user_name', 'remember_user', 'ok', 'search_phrase', 'search_keywords',
			'match_all', 'search_level', 'submit_button', 'is_trial', 'is_paid', 'uri_path',
			'url_parameters', 'form_parameters', 'base', 'path', 'query', 'request_uri', 'scheme',
			'host', 'port', 'get_parameter', 'set_parameter', 'reset_parameter'
		]
		,'functions' : [
			// Campsite objects
			'language', 'default_language', 'publication', 'default_publication', 'issue', 'default_issue',
			'article', 'default_article', 'article_attachment', 'audio_attachment', 'article_comment',
			'subtitle', 'image', 'topic', 'default_topic', 'user', 'subscription', 'template', 'default_template',
			'default_url', 'current_list', 'login_action', 'search_articles_action', 'submit_comment_action',
			'preview_comment_action', 'edit_user_action', 'edit_subscription_action'
		]
	}
	,'OPERATORS' :[
		'+', '-', '/', '*', '==', '<', '>', '%', '!', '&&', '||', '!=', '>=', '<=', '===', '!'
	]
	,'DELIMITERS' :[
		'(', ')', '[', ']', '{', '}'
	]
	,'REGEXPS' : {
		// highlight all variables ($...)
		'variables' : {
			'search' : '()(\\$\\w+)()'
			,'class' : 'variables'
			,'modifiers' : 'g'
			,'execute' : 'before' // before or after
		}
	}
	,'STYLES' : {
		'CONSTANTS': 'color: #CC0000'
		,'COMMENTS': 'color: #AAAAAA;'
		,'QUOTESMARKS': 'color: #879EFA;'
		,'KEYWORDS' : {
			'reserved' : 'color: #48BDDF;'
			,'functions' : 'color: #0040FD;'
			,'statements' : 'color: #60CA00;'
			}
		,'OPERATORS' : 'color: #FF00FF;'
		,'DELIMITERS' : 'color: #2B60FF;'
		,'REGEXPS' : {
			'variables' : 'color: #E0BD54;'
		}		
	}
};
