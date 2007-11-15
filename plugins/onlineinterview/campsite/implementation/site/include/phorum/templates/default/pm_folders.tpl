{include pm_folders.formstart}
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left">{LANG->PMFolderCreate}</div>
  <div class="PhorumStdBlock" style="padding-top: 15px; padding-bottom: 15px">
    <input type="text" name="create_folder_name" value="{CREATE_FOLDER_NAME}" size="20" maxlength="20" />
    <input type="submit" name="create_folder" value="{LANG->Submit}" class="PhorumSubmit" />
  </div>
</form>
{IF PM_USERFOLDERS}
  <br />
  {include pm_folders.formstart}
    <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left">{LANG->PMFolderRename}</div>
    <div class="PhorumStdBlock" style="padding-top: 15px; padding-bottom: 15px">
      <select name="rename_folder_from" style="vertical-align: middle">
        <option value="">{LANG->PMSelectAFolder}</option>
        {LOOP PM_USERFOLDERS}
          <option value="{PM_USERFOLDERS->id}">{PM_USERFOLDERS->name}</option>
        {/LOOP PM_USERFOLDERS}
      </select>
      {LANG->PMFolderRenameTo}
      <input type="text" name="rename_folder_to" value="{RENAME_FOLDER_NAME}" size="20" maxlength="20" />
      <input type="submit" name="rename_folder" value="{LANG->Submit}" class="PhorumSubmit" />
    </div>
  </form>
  <br />
  {include pm_folders.formstart}
    <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left">{LANG->PMFolderDelete}</div>
    <div class="PhorumStdBlock" style="padding-top: 15px; padding-bottom: 15px">
      {LANG->PMFolderDeleteExplain}<br /><br />
      {LANG->PMFolderDelete}
      <select name="delete_folder_target" style="vertical-align: middle">
        <option value="">{LANG->PMSelectAFolder}</option>
        {LOOP PM_USERFOLDERS}
          <option value="{PM_USERFOLDERS->id}">{PM_USERFOLDERS->name}{IF PM_USERFOLDERS->total} ({PM_USERFOLDERS->total}){/IF}</option>
        {/LOOP PM_USERFOLDERS}
      </select>
      <input type="submit" name="delete_folder" value="{LANG->Submit}" onclick="return confirm('{LANG->PMFolderDeleteConfirm}')" class="PhorumSubmit" />
    </div>
  </form>
{/IF}
