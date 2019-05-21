<?php

  require 'show_top.php';
  require 'database.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($errors, $input) = validate_form();
    if ($errors) {
      show_form($errors);
    } else {
      process_form($input);
    }
  } else {
    show_form();
  }

  function show_form($errors = array()) {
    if ($errors) {
      print '<ul><li>';
      print implode('</li><li>', $errors);
      print '</li></ul>';
    }
    // show_top.phpより。
    show_top_page();
  }

  function validate_form() {
    $errors = array();
    $input = array();

    //htmlentities: XSS対策
    $input['schedule-name'] = htmlentities($_POST['schedule-name']);
    $input['candidates'] = explode("\n", htmlentities($_POST['candidates']));
    return array($errors, $input);
  }

  function process_form($input) {
    print '予定名: ' . $input['schedule-name'] . '<br>';
    $candArray = $input['candidates'];
    print implode('<br>', $candArray);
    

    //あとで修正。　とりあえずこんなかんじでURLをつくる。
    //データベース検索して同じscheduleIdが存在しないか確認したほうがいい？
    //ほぼ重複しないと思うけど0%ではない？
    $scheduleId = md5(uniqid(mt_rand(), true));
    $schedule_url = $_SERVER['PHP_SELF'] . '/' . $scheduleId;

    print $schedule_url . '<br>';

    //データベースの処理追加する。
    //Array型かどうか確認と、名前がStringになってるか確認必要？
    registration($scheduleId, $input['schedule-name'], $input['candidates']);
    
  }
?>