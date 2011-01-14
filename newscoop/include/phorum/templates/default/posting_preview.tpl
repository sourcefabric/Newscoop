<div class="PhorumStdBlockHeader" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Preview}:</span>&nbsp;{LANG->PreviewExplain}
</div>
<div class="PhorumStdBlock" style="text-align: left">
  {IF PREVIEW->parent_id 0}
    <div class="PhorumReadBodySubject">{PREVIEW->subject}</div>
  {ELSE}
    <div class="PhorumReadBodyHead"><strong>{PREVIEW->subject}</strong></div>
  {/IF}
  <div class="PhorumReadBodyHead">{LANG->Postedby}: <strong>{PREVIEW->author}</strong></div>
  <div class="PhorumReadBodyHead">{LANG->Date}: {PREVIEW->datestamp}</div><br />
  <div class="PhorumReadBodyText">{PREVIEW->body}</div><br />
  {IF ATTACHMENTS}
    {IF PREVIEW->attachments}
      {LANG->Attachments}:
      {LOOP PREVIEW->attachments}
        {IF PREVIEW->attachments->keep}
          <span style="white-space:nowrap">
            <a href="{PREVIEW->attachments->url}">{PREVIEW->attachments->name} ({PREVIEW->attachments->size})</a>&nbsp;&nbsp;
          </span>
        {/IF}
      {/LOOP PREVIEW->attachments}
    {/IF}
  {/IF}
</div><br />
