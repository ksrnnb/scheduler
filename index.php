<?php

  require 'html_function.php';
  require 'database.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($errors, $input) = validate_form();
    if ($errors) {
      show_form($errors);
    } else {
      process_form($input);
    }
  } else {

    // TableぜんぶDropしたいとき。　あとで削除する
    // dropAllTables();
    if (isset($_GET['id'])) {
      //静的プレースホルダを利用しているので、SQLインジェクション対策は問題ない、と思う。
      $scheduleId = $_GET['id'];
      if (check_scheduleId($scheduleId)) {
        show_table($scheduleId);
      } else {
        //無効なidにアクセスした場合
        invalid_page();
      }
    } else {
      //ホーム以外は無効なアクセス
      if ($_SERVER['REQUEST_URI'] == '/') {
        show_form();
      } else {
        invalid_page();
      }
      
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

    createTables();
    registration($scheduleId, $input['schedule-name'], $input['candidates']);
    
    $schedule_url = '/?id=' . $scheduleId;
    //リダイレクトする。
    header('Location: ' . $schedule_url);
    exit;
  }
?>