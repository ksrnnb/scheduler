<?php

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

    require 'show_top.php';
  }

  function validate_form() {
    $errors = array();
    $input = array();

    //htmlentities: XSS対策
    $input['schedule-name'] = htmlentities($_POST['schedule-name']);
    $input['candidates'] = htmlentities($_POST['candidates']);

    return array($errors, $input);
  }

  function process_form($input) {
    print '予定名: ' . $input['schedule-name'] . '<br>';
    $candArray = explode("\n", $input['candidates']);
    print implode('<br>', $candArray);
  }
?>