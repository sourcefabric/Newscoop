<?php
  // CREATING ENVIRONMENT
  require_once 'conf.php';

  // If the user hasn't the right to change news -> access denied.
  if (!$LU->checkRight(RIGHT_NEWS_CHANGE)) {
      $tpl->loadTemplatefile('news_notallowed.tpl.php', false, false);
      include_once 'finish.inc.php';
      exit();
  }

  // Read form data.
  $action  = array_key_exists('action', $_GET)   ? $_GET['action']   : '';
  $action  = array_key_exists('action', $_POST)  ? $_POST['action']  : $action;
  $news_id = array_key_exists('news_id', $_GET)  ? (int)$_GET['news_id']  : 0;
  $news_id = array_key_exists('news_id', $_POST) ? (int)$_POST['news_id'] : $news_id;

  /**
   * Page for changing news.
   */
  if ($action == 'change' AND $news_id != 0) {

      $newsRow = $db->queryRow('SELECT
                                  news_id,
                                  ROUND((TO_DAYS(valid_to)-TO_DAYS(created_at))/7) AS weeks,
                                  UNIX_TIMESTAMP(created_at) AS created_at,
                                  news,
                                  owner_user_id,
                                  owner_group_id
                              FROM
                                  news
                              WHERE
                                  news_id = ' . $news_id);

      // Check whether the user is cheating.
      if (!$LU->checkRightLevel(RIGHT_NEWS_CHANGE, (int)$newsRow['owner_user_id'], (int)$newsRow['owner_group_id'])) {
          header('Location: news_change.php?logout=1');
          exit();
      } else {

          // Read form data.
          $news     = array_key_exists('news', $_POST)     ? $_POST['news'] : '';
          $valid_to = array_key_exists('valid_to', $_POST) ? (int)$_POST['valid_to'] : '';

          if (!empty($news)) {

              if (!ereg('^[1-9][0-9]?$', $valid_to)) {
                  $errorMsg = '<p><span style="color: red;">Only numbers between 1 and 99 are allowed here.</span></p>';
              } else {
              // Form seems to be correct. Write data into the db.
                  $news = str_replace("\r\n",'<br />',$news);

                  $db->query('UPDATE
                                  news
                              SET
                                  valid_to = "' . date('Y.m.d H:i:s', $newsRow['created_at']+60*60*24*7*$valid_to) . '",
                                  news = "' . addslashes( $news ) . '"
                              WHERE
                                  news_id = "' . $news_id . '"');

                  // Clear action.
                  $action = '';
              }

          }

          // Show page to change the news.
          if (empty($news) OR isset($errorMsg)) {
              $tpl->loadTemplatefile('news_new.tpl.php');

              $tpl->setVariable('form_action', 'news_change.php');
              $tpl->touchBlock('button_abort');

              if (!empty($news)) {
                  $tpl->setVariable('message', $news);
              } else {
                  $tpl->setVariable('message', str_replace('<br />', "\r\n", stripslashes($newsRow['news'])));
              }

              if (!empty($valid_to)) {
                  $tpl->setVariable('valid', $valid_to);
              } else {
                  $tpl->setVariable('valid', $newsRow['weeks']);
              }

              if (isset($errorMsg)) {
                  $tpl->setVariable('script_msg', $errorMsg);
              }

              $tpl->setVariable('news_id', $news_id);
              $tpl->touchBlock('action');

          }

      }

  } // End $action == 'change'


  /**
   * Page to delete news.
   */
  if ($action == 'delete' AND $news_id != 0) {

      $rightInfo = $db->queryRow('SELECT
                                    owner_user_id,
                                    owner_group_id
                                FROM
                                    news
                                WHERE
                                    news_id = ' . (int)$news_id);

      // Check whether the user is cheating.
      if (!$LU->checkRightLevel(RIGHT_NEWS_DELETE, (int)$rightInfo['owner_user_id'], (int)$rightInfo['owner_group_id'])) {
          header('Location: news_change.php?logout=1');
      } else {
          $confirmed = array_key_exists('is_js_confirmed', $_GET) ? $_GET['is_js_confirmed'] : 0;

          if ($confirmed) {
              $db->query('DELETE FROM
                              news
                          WHERE
                              news_id = ' . (int)$news_id);
              // Clear action.
              $action = '';
          }
      }

  } // End $action == 'loeschen'


  /**
   * Show summary.
   */
  if (empty($action)) {

      $tpl->loadTemplatefile('news_change.tpl.php');

      // Get the last five news.
      $res = $db->query('SELECT
                             N.news_id,
                             DATE_FORMAT(N.created_at,"%d.%m.%Y - %H:%i") AS date,
                             N.news,
                             N.owner_user_id,
                             N.owner_group_id,
                             U.handle
                         FROM
                             news AS N
                         INNER JOIN
                             liveuser_perm_peoples AS PU
                         ON
                             N.owner_user_id = PU.perm_user_id
                         INNER JOIN
                             liveuser_peoples AS U
                         ON
                             PU.auth_user_id = U.authUserId
                         ORDER BY
                             N.created_at DESC');

      $bgcolor = array('#DDDDDD', '#CCCCCC');
      $counter = 0;

      while ($row = $res->fetchRow()) {
          $tpl->setCurrentBlock('row');
          $tpl->setVariable(array('color_n' => $bgcolor[$counter++%2],
                                  'color_h' => '#D3DCE3',
                                  'time'    => $row['date'] . ' Uhr',
                                  'news'    => substr(stripslashes($row['news']), 0, 20) . ' ...',
                                  'author'  => '<a href="mailto:' . $row['handle'] . '@your-company.com">' . $row['handle'] . '</a>'));

          // Has the user the right to change the news?
          if ($LU->checkRightLevel(RIGHT_NEWS_CHANGE, (int)$row['owner_user_id'], (int)$row['owner_group_id'])) {
              $tpl->setVariable('link_change', 'news_change.php?action=change&news_id='.$row['news_id']);
          }

          // Has the user the right to delete the news?
          if ($LU->checkRightLevel(RIGHT_NEWS_DELETE, (int)$row['owner_user_id'], (int)$row['owner_group_id'])) {
              $tpl->setVariable('link_delete', 'news_change.php?action=delete&news_id='.$row['news_id'].'" onclick="return confirmLink(this, \'Shall I really delete \\\''.htmlentities(substr(str_replace('<br>', ' ', $row['news']), 0, 20), ENT_QUOTES).' ...\\\' ?\')');
          }

          $tpl->parseCurrentBlock();
      }

  } // End empty($action)


  include_once 'finish.inc.php';
?>
