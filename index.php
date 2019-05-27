<?php

  require 'show_top.php';
  require 'show_table.php';
  require 'database.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($errors, $input) = validate_form();
    if ($errors) {
      show_form($errors);
    } else {
      process_form($input);
    }
  } else {
    if (isset($_GET['id'])) {
      //ここ脆弱性確認する！！　OSコマンドインジェクション？？
      show_table($_GET['id']);
    } else {
      show_form();
    }
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
    $input['candidates'] = explode('-', htmlentities($_POST['candidates']));
    return array($errors, $input);
  }

  function process_form($input) {

    //あとで修正。　とりあえずこんなかんじでURLをつくる。
    //データベース検索して同じscheduleIdが存在しないか確認したほうがいい？
    //ほぼ重複しないと思うけど0%ではない？
    $scheduleId = md5(uniqid(mt_rand(), true));

    registration($scheduleId, $input['schedule-name'], $input['candidates']);
    
    $schedule_url = $_SERVER['PHP_SELF'] . '/?id=' . $scheduleId;
    //リダイレクトする。
    header('Location: ' . $schedule_url);
    exit;
  }
?>