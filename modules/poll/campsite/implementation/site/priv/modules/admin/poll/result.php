<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Poll result');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll         = Input::Get('poll', 'array', array());
    $target_lang  = Input::Get('target_lang');
  ?>
  <form name='language' action='result.php'>
  <table border="0" width="100%">
  <tr bgcolor="#C0D0FF">
    <td><b><?php putGS ("results"); ?></b></td>
    <td align="right">
      <?php putGS("target lang"); ?>:
      <?php
      if (!$lang) {
          $lang = $defaultIdLanguage;
      }
      langmenu ("target_lang");
      ?>
      <input type="hidden" name="poll[Number]" value="<?php print $poll['Number']; ?>">
    </td>
  </tr>

  <?php             
  ##### query target_language
  $query = "SELECT * 
            FROM    mod_poll_questions
            WHERE   NrPoll      =   {$poll['Number']} AND 
                    IdLanguage  =   $target_lang 
            LIMIT   0,1";
  $quest = sqlROW($DB['modules'], $query);

  if (!is_array ($quest)) {
      // if not user query default langauge
      $query = "SELECT * 
                FROM mod_poll_questions
                WHERE   NrPoll      =   {$poll['Number']} AND 
                        IdLanguage  =   $defaultIdLanguage 
                LIMIT   0,1";
      $quest = sqlROW($DB['modules'], $query);
  }
  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td><?php putGS("title"); ?></td><td><?php print $quest['Title']; ?></td>
  </tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td><?php putGS("question"); ?></td><td><?php print $quest['Question']; ?></td>
  </tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php
  ##### overall languages #########################

  ##### get answer-terms in given/default langauge
  $query = "SELECT * 
            FROM    mod_poll_answers
            WHERE   NrPoll      = {$poll['Number']} AND 
                    IdLanguage  = {$quest['IdLanguage']} 
            ORDER BY NrAnswer";
  $answers = sqlQuery($DB['modules'], $query);

  while ($ans = mysql_fetch_array($answers)) {
      print "<tr ";
      if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
      print "><td>".getGS("answer")." {$ans['NrAnswer']})</td><td>{$ans['Answer']}</td></tr>";
  }

  ##### sum of votes
  $query = "SELECT  SUM(NrOfVotes) AS allsum 
            FROM    mod_poll_answers
            WHERE   NrPoll = {$poll['Number']}";
  $sum = sqlRow($DB['modules'], $query);

  ##### sum of votes depending to nr_answer, independing from IdLanguage
  $query = "SELECT  NrAnswer, 
                    SUM(NrOfVotes) as rowsum 
            FROM    mod_poll_answers
            WHERE   NrPoll  = {$poll['Number']} 
            GROUP   BY NrAnswer 
            ORDER   BY NrAnswer";
  $votes = sqlQuery($DB['modules'], $query);
  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>><td colspan="2">&nbsp;</td></tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
    <td colspan="2">Votes Overall Languages (<?php echo $sum['allsum']; ?> Votes)</td>
  </tr>
  <?php
  mysql_data_seek ($answers, 0);
  
  while ($polld = mysql_fetch_array($answers)) {
      $vote = mysql_fetch_array($votes);
      if ($vote['rowsum']) {
          $prozent = round (100/$sum[allsum]*$vote[rowsum],1);
      } else { 
          $prozent = 0;
      }
    ?>
    <tr <?php
    if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php }
    else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
      ?>>
      <td><?php echo "{$polld['NrAnswer']}) Votes:  {$vote['rowsum']} ($prozent %)"; ?></td>
      <td>
      <?php
      for ($n=0; $n<=$prozent; $n++) echo "I";
      ?></td>
    </tr>
    <?php
  }
  ##### end overall languages #########################
  /*
  ##### Diff by languages #############################
  $langq = "SELECT pq.IdLanguage, cl.Name
  FROM mod_poll_questions AS pq, campsite.Languages AS cl
  WHERE NrPoll=$poll[id] AND pq.IdLanguage=cl.Number
  ORDER BY IdLanguage";

  $langr = sqlQuery($DB[poll], $langq);
  while ($lang = mysql_fetch_array ($langr))
  {
  ##### sum of votes dep. to language
  $query = "SELECT SUM(votes) AS allsum FROM mod_poll_answers
  WHERE NrPoll=$poll[id] AND IdLanguage=$lang[IdLanguage]";
  $sumlang = sqlRow ($DB['poll'], $query);

  ##### sum of votes dep. to nr_answer, depending from IdLanguage
  $query = "SELECT nr_answer, SUM(votes) as rowsum FROM mod_poll_answers
  WHERE NrPoll=$poll[id] AND IdLanguage=$lang[IdLanguage]
  GROUP BY nr_answer ORDER BY nr_answer";
  $votes = sqlQuery($DB['poll'], $query);

  ?>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>><td colspan="2">&nbsp;</td></tr>
  <tr <?php if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php } else { $color=1; ?>BGCOLOR="#D0D0D0"<?php } ?>>
  <td colspan="2">Votes on <?php echo "$lang[Name]: $sumlang[allsum] (".(100/$sum[allsum]*$sumlang[allsum])."% of Overall)"; ?></td>
  </tr>
  <?php
  mysql_data_seek ($answers, 0);
  while ($polld = mysql_fetch_array ($answers))
  {
  $vote = mysql_fetch_array ($votes);
  if ($vote[rowsum]) $prozent = round (100/$sumlang[allsum]*$vote[rowsum],1);
  else $prozent = 0;
  ?>
  <tr <?php
  if ($color) { $color=0; ?>BGCOLOR="#D0D0B0"<?php }
  else { $color=1; ?>BGCOLOR="#D0D0D0"<?php }
  ?>>
  <td><?php echo "$polld[nr_answer]) Votes:  $vote[rowsum] ($prozent %)"; ?></td>
  <td>
  <?php
  for ($n=0; $n<=$prozent; $n++) echo "I";
  ?></td>
  </tr>
  <?php
  }
  ##### end diff by languages #########################
  }
  */
  ?>

  </table>
  </form>
  <?php
}
?>
