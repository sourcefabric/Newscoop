{{ include file="_tpl/_html-head.tpl" }}
{{ include file="_tpl/jqm_section.tpl" }}

{{*

If you want to run the mobile templates parallel to your
usual templates, use the following IF check for mobile 
devices. Attention: this might create problems with the 
caching. Make sure to also have two templates for the 
header - you only load the jQueryMobile related .js
and .css files.

{{ if $gimme->browser->ua_type == "mobile" }}
  {{ include file="_tpl/jqm_section.tpl" }}
{{ else }}
<!-- desktop templates here -->
{{ /if }}

*}}
