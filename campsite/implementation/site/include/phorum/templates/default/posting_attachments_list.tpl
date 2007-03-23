<b>{LANG->Attachments}:</b>
<table id="phorum-attachment-list" class="PhorumFormTable" cellspacing="0" width="100%">
  {ASSIGN LIST POST->attachments}
  {LOOP LIST}
    {IF LIST->keep}
      <tr>
        <td>{LIST->name} ({LIST->size})</td>
        <td align="right">
          {HOOK tpl_editor_attachment_buttons LIST}
          <input type="submit" name="detach:{LIST->file_id}" value="{LANG->Detach}" class="PhorumSubmit" />
        </td>
      </tr>
    {/IF}
  {/LOOP LIST}
</table>
