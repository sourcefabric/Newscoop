<?php



$publications = Publication::GetPublications();
?>
<form action="" name="section_rights" method="post">
<table border="0" cellspacing="0" cellpadding="3" align="left">
<?php
foreach($publications as $publication) {
?>
<tr>
  <td>
  <?php
    p('Publication: <strong>'.$publication->getName().'</strong>');
    $issues = Issue::GetIssues($publication->getPublicationId());
    foreach($issues as $issue) {
  ?>
    <table>
    <tr>
      <td colspan="3">
        <?php p('&nbsp;&nbsp;Issue: '.$issue->getName().' ('.$issue->getLanguageName().')'); ?>
      </td>
    </tr>
    <?php
        $sections = Section::GetSections($publication->getPublicationId(),
                                         $issue->getIssueNumber(),
                                         $issue->getLanguageId());
        foreach($sections as $section) {
            $right_name = $section->getSectionRightName();
    ?>
    <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td>
        <input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" <?php if ($editUser->hasPermission($right_name)) { p("CHECKED"); } ?> />
      </td>
      <td style="padding-right: 10px;">
        <?php p($section->getName() . ' (<em>' . $right_name . '</em>)'); ?>
      </td>
    </tr>
<?php
        }
?>
    </table>
<?php
    }
?>
  </td>
</tr>
<?php
}
?>
</table>
</form>
