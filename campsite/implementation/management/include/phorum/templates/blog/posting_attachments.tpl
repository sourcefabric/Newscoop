<div class="PhorumStdBlockHeader PhorumNarrowBlock">

{IF POST->attachments}
  {include posting_attachments_list}

  {ASSIGN AttachPhrase LANG->AttachAnotherFile}

{ELSE}
  {ASSIGN AttachPhrase LANG->AttachAFile}
{/IF}

{IF ATTACHMENTS_FULL}
  <b>{LANG->AttachFull}</b>
{ELSE}
  <script type="text/javascript">
  function phorumShowAttachForm() {
    document.getElementById('phorum-attach-link').style.display='none';
    document.getElementById('phorum-attach-form').style.display='block';
  }
  document.write("<div id=\"phorum-attach-link\" style=\"display: block;\"><a href=\"javascript:phorumShowAttachForm();\"><b>{AttachPhrase} ...</b></a></div>\n");
  document.write("<div id=\"phorum-attach-form\" style=\"display: none;\">");
  </script>

  <b>{AttachPhrase}</b>

  <ul>
  {IF EXPLAIN_ATTACH_FILE_TYPES}<li>{EXPLAIN_ATTACH_FILE_TYPES}{/IF}
  {IF EXPLAIN_ATTACH_FILE_SIZE}<li>{EXPLAIN_ATTACH_FILE_SIZE}{/IF}
  {IF EXPLAIN_ATTACH_TOTALFILE_SIZE}<li>{EXPLAIN_ATTACH_TOTALFILE_SIZE}{/IF}
  {IF EXPLAIN_ATTACH_MAX_ATTACHMENTS}<li>{EXPLAIN_ATTACH_MAX_ATTACHMENTS}{/IF}
  </ul>

  <input type="file" size="50" name="attachment" />
  <input type="submit" name="attach" value="{LANG->Attach}" />
<script type="text/javascript">document.write('</div>');</script>
{/IF}

</div>
