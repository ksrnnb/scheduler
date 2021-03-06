<?php
  require 'database.php';
  require 'html_function.php';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($errors, $input) = validate_form();
    if ($errors) {
      show_form($errors);
    } else {
      process_form($input);
    }
  } else {
    //不正なリクエスト
    invalid_page();
  }

  function show_form($errors = array()) {
    if ($errors) {
      print '<ul><li>';
      print implode('</li><li>', $errors);
      print '</li></ul>';
    }
  }

  function validate_form() {
    $errors = array();
    $input = array();

    //htmlentities: XSS対策
    $input['scheduleId'] = htmlentities($_POST['scheduleId']);
    $input['candidates'] = htmlentities($_POST['candidates']);
    $input['user'] = htmlentities($_POST['user']);
    $input['availability'] = htmlentities($_POST['availability']);
    $input['userId'] = htmlentities($_POST['userId']);

    return array($errors, $input);
  }

  function process_form($input) {
    user_registration($input['scheduleId'], $input['candidates'], $input['user'], $input['availability'], $input['userId']);
    
    $schedule_url =  '/?id=' . $input['scheduleId'];
    //リダイレクトする。
    header('Location: ' . $schedule_url);
  }
    
?>