<?php
  require 'database.php';
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list($errors, $input) = validate_form();
    if ($errors) {
      show_form($errors);
    } else {
      process_form($input);
    }
  } else {
      //不正なリクエスト
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

    return array($errors, $input);
  }

  function process_form($input) {
    user_registration($input['scheduleId'], $input['candidates'], $input['user'], $input['availability']);
  }
    
?>