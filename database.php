<?php
  require 'variable.php';
  try {
    $db = new PDO('mysql:host=192.168.33.10;dbname=scheduler', $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチスタイルをオブジェクトに
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
  } catch (PDOException $e) {
    print $e;
  }

  createDatabase($db);

  function createDatabase($db) {
    $db->exec("CREATE TABLE IF NOT EXISTS User (
      userId INT NOT NULL AUTO_INCREMENT,
      userName VARCHAR(16),
      scheduleId CHAR(32),
      PRIMARY KEY(userId)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Schedule (
      scheduleId CHAR(32) PRIMARY KEY,
      scheduleName VARCHAR(64)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Candidate (
      candidateId INT NOT NULL AUTO_INCREMENT,
      candidate VARCHAR(16),
      scheduleId CHAR(32),
      PRIMARY KEY(candidateId)
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS Availability (
      availability VARCHAR(32),
      scheduleId CHAR(32),
      userId INT NOT NULL,
      candidateId INT
    )");
  }

  function registration($scheduleId, $schedule_name, $candidates) {
    global $db;
    $q = $db->prepare("INSERT INTO Schedule (scheduleId, scheduleName) VALUES (?, ?)");
    $q->execute(array($scheduleId, $schedule_name));
    
    foreach ($candidates as $candidate) {
      
      $q = $db->prepare("INSERT INTO Candidate (candidate, scheduleId) VALUES (?, ?)");
      $q->execute(array($candidate, $scheduleId));
    }
    
  }

  function get_schedule($scheduleId) {
    global $db;
    //スケジュール名、候補日、すでに登録したユーザーをとってきて返す。
    //schedule_name: string, candidates: array, users: array, availabilities: array(array)

    //Schedule, Candidate, User, Availabilityを結合する？
    $qs = $db->prepare("SELECT scheduleName from Schedule where scheduleId = ?");
    $qs->execute(array($scheduleId));
    $schedule_name = $qs->fetch()->scheduleName;

    $qc = $db->prepare("SELECT candidate from Candidate where scheduleId = ?");
    $qc->execute(array($scheduleId));
    $candidates = [];
    while ($row = $qc->fetch()) {
      array_push($candidates, $row->candidate);
    }

    //ユーザーとAvailabilitiesをとってきて同じ形式で返す。
    $qu = $db->prepare("SELECT userName, userId from User where scheduleId = ?");
    $qu->execute(array($scheduleId));
    $users = [];
    $userIds = [];
    while ($row = $qu->fetch()) {
      array_push($users, $row->userName);
      array_push($userIds, $row->userId);
    }

    //availabilitiesを同じ形で返すように。。。
    $availabilities = [];
    foreach ($userIds as $userId) {
      $qa = $db->prepare("SELECT availability, candidate from Availability INNER JOIN Candidate 
                          ON Availability.candidateId = Candidate.candidateId where Candidate.scheduleId = ? and userId = ?");
      $qa->execute(array($scheduleId, $userId));
      // ここは毎回初期化しとく
      $availability_array = [];
      while ($row = $qa->fetch()) {
        $availability_array = array_merge($availability_array, array($row->candidate => $row->availability));
      }
      //userIdからuserNameとってくる
      $qu = $db->prepare("SELECT userName from User where userId = ?");
      $qu->execute(array($userId));
      $availabilities = array_merge($availabilities, array($qu->fetch()->userName => $availability_array));
    }
    // $availabilities = array('user1' => array('2019/4/1' => 0, '2019/5/21' => 0, '2019/5/24' => 1), 'user2' => array('2019/4/1' => 1, '2019/5/21' => 1, '2019/5/24' => 0));

    return array($schedule_name, $candidates, $users, $availabilities);

  }

  function get_symbol_sum($scheduleId, $candidate) {
    global $db;

    // いっかいMySQLで確認する。各UserのAvailabilityがでてくればいいかんじ。それに対してループ組んでやればいいかと
    $q = $db->prepare("SELECT availability from Availability
                      INNER JOIN Candidate ON Availability.candidateId = Candidate.candidateId
                      where Candidate.scheduleId = ? and Candidate.candidate = ?");
    $q->execute(array($scheduleId, $candidate));

    // 初期化
    $symbol_sum = array(0, 0, 0);
    while ($row = $q->fetch()) {
      $symbol_sum[$row->availability]++;
    }

    return $symbol_sum;

  }

  function user_registration($scheduleId, $candidates, $user, $availabilities) {
    global $db;
    //availabilitiesを数値の配列に変換
    $availabilities = explode('-', $availabilities);
    foreach ($availabilities as $i => $availability) {
      $availabilities[$i] = intval($availability);
    }
    //ユーザーの登録
    $q = $db->prepare("INSERT INTO User (userName, scheduleId) VALUES (?, ?)");
    $q->execute(array($user, $scheduleId));

    // 登録したユーザーのIDを取得（AUTO INCREMENTのやつ、登録時にとってこれたらいいのに。。。）
    $qu = $db->prepare("SELECT userId from User where userName = ? AND scheduleId = ?");
    $qu->execute(array($user, $scheduleId));
    $userId = $qu->fetch()->userId;

    $candidates = explode('-', $candidates);
    foreach ($candidates as $i => $candidate) {

      $qc = $db->prepare("SELECT candidateId from Candidate where candidate = ? AND scheduleId = ?");
      $qc->execute(array($candidate, $scheduleId));
      $candidateId = $qc->fetch()->candidateId;

      //  availabilityの登録
      $qa = $db->prepare("INSERT INTO Availability (availability, scheduleId, userId, candidateId) VALUES (?, ?, ?, ?)");
      $qa->execute(array($availabilities[$i], $scheduleId, $userId, $candidateId));

    }

  }

?>