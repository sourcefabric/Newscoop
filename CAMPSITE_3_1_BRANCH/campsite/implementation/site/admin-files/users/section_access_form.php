<?php

$publications = Publication::GetPublications();
?>
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
      <td colspan="2" style="padding-left: 10px;">
        <?php p('Issue: '.$issue->getName().' ('.$issue->getLanguageName().')'); ?>
      </td>
    </tr>
    <?php
        $sections = Section::GetSections($publication->getPublicationId(),
                                         $issue->getIssueNumber(),
                                         $issue->getLanguageId());
        $sectionsNo = sizeof($sections);
        foreach($sections as $section) {
            $right_name = $section->getSectionRightName();
    ?>
    <tr>
      <td align="left" width="20" style="padding-left: 20px;">
        <input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" <?php if ($editUser->hasPermission($right_name)) { p("CHECKED"); } ?> />
      </td>
      <td align="left" style="padding-left: 5px;">
        <?php p($section->getName()); ?>
      </td>
    </tr>
<?php
        }
        if ($sectionsNo < 1) {
?>
    <tr>
         <td style="padding-left: 25px;"><?php putGS("No sections"); ?></td>
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
