{{ include file="fastnews/htmlheader.tpl" }}

<!-- This is the article template -->

<html>
<head>
<title>{{ $campsite->article->name }} ({{ $campsite->publication->name }}, #{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} -- current issue{{ /if }}</title>

{{ include file="fastnews/meta.tpl" }}

</head>
<body>

{{ include file="fastnews/header.tpl" }}

{{ include file="fastnews/sitemap-article.tpl" }}

<td valign=top>

<div class="rightfloat">
{{ include file="fastnews/userinfo.tpl" }}
{{ include file="fastnews/printbox.tpl" }}
{{ include file="fastnews/subnav.tpl" }}
{{ include file="fastnews/topics.tpl" }}
</div>

<div style="max-width:42em;" align=left>
{{ if $campsite->article->type_name == "extended" }}
{{ include file="fastnews/article-extended.tpl" }}
{{ /if }}
{{ if $campsite->article->type_name == "fastnews" }}
{{ include file="fastnews/article-fastnews.tpl" }}
{{ /if }}
</div>

{{ if $campsite->article->comments_enabled }}
<table>
    <tr>
        <td>
            {{ if ! $campsite->submit_comment_action->is_error }}
                <a name="comments"></a>
            {{ /if }}
            {{ list_article_comments order="byDate asc" }}
                {{ if $campsite->current_list->at_beginning }}
                    <p>Comments:</p>
                {{ /if }}
                <table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
                    cellpadding="0" id="comment_{{ $campsite->comment->identifier }}">
                    <tr>
                        <td valign="top" align="left" style="background-color: #AAAAAA; padding: 3px;">
                            {{ $campsite->comment->subject }}
                        </td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" style=" font-size: 8pt; padding: 3px;">
                            Posted on {{ $campsite->comment->submit_date }}
                            by <b>{{ $campsite->comment->reader_email|obfuscate_email }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="left" style="padding: 3px">
                            {{ $campsite->comment->content }}
                        </td>
                    </tr>
                </table>
<script>
document.getElementById("comment_{{ $campsite->comment->identifier }}").style.padding-left=10*{{ $campsite->comment->level }}+"px";
</script>
                {{ /list_article_comments }}
                {{ if $campsite->prev_list_empty }}
                    <p>No comments have been posted.</p>
                {{ /if }}
        </td>
    </tr>

    <tr>
        <td style="padding-top: 15px;">
            {{ if $campsite->submit_comment_action->is_error }}
                <a name="comments"></a>
            {{ /if }}
            <div id="articleComment">
                        {{ comment_form submit_button="Submit comment" anchor="comments" }}
            {{ formparameters options="articlecomment" }}

            <table cellpadding="3" style="border:1px solid black;">
            <tr>
                <td colspan="2">
                    <b>Add a comment</b>
                    <br/><span style="color: red">{{ $campsite->submit_comment_action->error_message }}</span>
                </td>
            </tr>
            <tr>
                <td>Your name/email:</td>
                <td>
                    {{ if $campsite->user->logged_in }}
                        <p>{{ $campsite->user->email }}</p>
                    {{ else }}
                        {{ camp_edit object="comment" attribute="reader_email" }}
                    {{ /if }}
                </td>
            </tr>
            <tr>
                <td>Subject:</td>
                <td>{{ camp_edit object="comment" attribute="subject" }}</td>
            </tr>
            <tr>
                <td valign="top">Comment:</td>
                <td>{{ camp_edit object="comment" attribute="content" }}</td>
            </tr>

    {{ if $campsite->publication->captcha_enabled }}
            <tr>
                <td colspan="2" align="center">
                    Type in the code below (used to prevent spam):
                    {{ camp_edit object="captcha" attribute="code" }}<br>
                    <img src="{{ captcha_image_link }}">
                </td>
            </tr>
    {{ /if }} <!-- end if articleComment CAPTCHAEnabled -->
            </table>
{{ /comment_form }}
</div>
        </td>
    </tr>
{{ /if }} <!-- end if articleComment enabled -->
</table>

</td>


{{ include file="fastnews/footer.tpl" }}
</body>
</html>
