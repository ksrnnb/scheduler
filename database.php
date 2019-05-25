<?php
  require 'variable.php';
  try {
    $db = new PDO('mysql:host=192.168.33.10;dbname=scheduler', $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    print $e;
  }

  createDatabase($db);

  function createDatabase($db) {
    $db->exec("CREATE TABLE IF NOT EXISTS User (
      userId INT NOT NULL AUTO_INCREMENT,
      userName VARCHAR(16),
      scheduleId CHAR(32),
      PRIMARY KEY(userId, scheduleId)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Schedule (
      scheduleId CHAR(32) PRIMARY KEY,
      scheduleName VARCHAR(64)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Candidate (
      candidateId INT NOT NULL AUTO_INCREMENT,
      candidate VARCHAR(16),
      scheduleId CHAR(32),
      PRIMARY KEY(candidateId, scheduleId)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Availability (
      availability VARCHAR(32),
      scheduleId CHAR(32),
      userId INT NOT NULL,
      candidateId INT PRIMARY KEY
    )");
  }

  function registration($scheduleId, $schedule_name, $candidates) {
    global $db;
    $q = $db->prepare("INSERT INTO Schedule (scheduleId, scheduleName) VALUES (?, ?)
    ");
    $q->execute(array($scheduleId, $schedule_name));
    
    foreach ($candidates as $candidate) {
      
      $q = $db->prepare("INSERT INTO Candidate (candidate, scheduleId) VALUES (?, ?)");
      $q->execute(array($candidate, $scheduleId));
    }
    
  }

  function get_schedule($id) {
    global $db;
    //スケジュール名、候補日、すでに登録したユーザーをとってきて返す。
    //schedule_name: string, candidates: array, users: array

    //とりあえず仮で適当なものをreturnする。
    $schedule_name = 'よてい';
    $candidates = ['4/29', '5/21', '5/24'];
    $users = ['user1', 'user2'];
    $availabilities = array('user1' => array('4/29' => 0, '5/21' => 0, '5/24' => 1), 'user2' => array('4/29' => 1, '5/21' => 1, '5/24' => 0));

    
    // availabilityはどうする？
    return array($schedule_name, $candidates, $users, $availabilities);

  }

?>