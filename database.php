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

?>