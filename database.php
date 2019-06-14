<?php
  require 'variable.php';
  

  $uri = $_SERVER['REQUEST_URI'];
  if (! strpos($uri, 'database.php') === false) {
    // index.phpからきてないときは読み込む必要がある
    require 'html_function.php';
    invalid_page();
  }

  try {
    $opt = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
                 PDO::ATTR_EMULATE_PREPARES => false,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ);
    $db = new PDO('mysql:host=192.168.33.10;dbname=scheduler', $db_user, $db_password, $opt);

  } catch (PDOException $e) {
    print $e;
  }

  function createTables() {
    global $db;
    $db->exec("CREATE TABLE IF NOT EXISTS User (
      userId INT NOT NULL AUTO_INCREMENT,
      userName VARCHAR(32),
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

  function check_scheduleId($scheduleId) {
    global $db;
    $q = $db->prepare("SELECT * FROM Schedule WHERE scheduleId = ?");
    $q->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $q->execute();
    if ($q->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  function registration($scheduleId, $schedule_name, $candidates) {
    global $db;
    $q = $db->prepare("INSERT INTO Schedule (scheduleId, scheduleName) VALUES (?, ?)");
    $q->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $q->bindValue(2, $schedule_name, PDO::PARAM_STR);
    $q->execute();
    
    foreach ($candidates as $candidate) {
      
      $q = $db->prepare("INSERT INTO Candidate (candidate, scheduleId) VALUES (?, ?)");
      $q->bindValue(1, $candidate, PDO::PARAM_STR);
      $q->bindValue(2, $scheduleId, PDO::PARAM_STR);
      $q->execute();
    }
    
  }

  function dropAllTables() {
    global $db;
    $db->exec("DROP TABLE User");
    $db->exec("DROP TABLE Candidate");
    $db->exec("DROP TABLE Availability");
    $db->exec("DROP TABLE Schedule");
  }

  function get_schedule($scheduleId) {
    global $db;
    //スケジュール名、候補日、すでに登録したユーザーをとってきて返す。
    //schedule_name: string, candidates: array, users: array, availabilities: array(array)

    //Schedule, Candidate, User, Availabilityを結合する？
    $qs = $db->prepare("SELECT scheduleName from Schedule where scheduleId = ?");
    $qs->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $qs->execute();
    $schedule_name = $qs->fetch()->scheduleName;

    $qc = $db->prepare("SELECT candidate from Candidate where scheduleId = ?");
    $qc->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $qc->execute();
    $candidates = [];
    while ($row = $qc->fetch()) {
      array_push($candidates, $row->candidate);
    }

    //ユーザーとAvailabilitiesをとってきて同じ形式で返す。
    $qu = $db->prepare("SELECT userName, userId from User where scheduleId = ?");
    $qu->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $qu->execute();
    $users = [];
    while ($row = $qu->fetch()) {
      //数値をそのままキーにできない。。。
      $users = array_merge($users, array('id' . $row->userId => $row->userName));
    }

    //availabilitiesを同じ形で返すように。。。
    $availabilities = [];
    foreach ($users as $userId => $userName) {
      $userId_int = intval(trim($userId, 'id'));
      $qa = $db->prepare("SELECT availability, candidate from Availability INNER JOIN Candidate 
                          ON Availability.candidateId = Candidate.candidateId where Candidate.scheduleId = ? and userId = ?");
      $qa->bindValue(1, $scheduleId, PDO::PARAM_STR);
      $qa->bindValue(2, trim($userId, 'id'), PDO::PARAM_INT);
      $qa->execute();
      // ここは毎回初期化しとく
      $availability_array = [];
      while ($row = $qa->fetch()) {
        $availability_array = array_merge($availability_array, array($row->candidate => $row->availability));
      }
      // userIdじゃないと重複する可能性がある。
      $availabilities = array_merge($availabilities, array($userId => $availability_array));
    }
    // $availabilities = array('userId1' => array('2019/4/1' => 0, '2019/5/21' => 0, '2019/5/24' => 1), 'userId2' => array('2019/4/1' => 1, '2019/5/21' => 1, '2019/5/24' => 0));

    return array($schedule_name, $candidates, $users, $availabilities);

  }

  function get_symbol_sum($scheduleId, $candidate) {
    global $db;

    // いっかいMySQLで確認する。各UserのAvailabilityがでてくればいいかんじ。それに対してループ組んでやればいいかと
    $q = $db->prepare("SELECT availability from Availability
                      INNER JOIN Candidate ON Availability.candidateId = Candidate.candidateId
                      where Candidate.scheduleId = ? and Candidate.candidate = ?");
    $q->bindValue(1, $scheduleId, PDO::PARAM_STR);
    $q->bindValue(2, $candidate, PDO::PARAM_INT);
    $q->execute();

    // 初期化
    $symbol_sum = array(0, 0, 0);
    while ($row = $q->fetch()) {
      $symbol_sum[$row->availability]++;
    }

    return $symbol_sum;

  }

  function user_registration($scheduleId, $candidates, $user, $availabilities, $userId = '') {
    global $db;
    //availabilitiesを数値の配列に変換
    $availabilities = explode('-', $availabilities);
    foreach ($availabilities as $i => $availability) {
      $availabilities[$i] = intval($availability);
    }

    if ($userId) {
      $userId = intval(trim($userId, 'id'));
      $qu = $db->prepare("UPDATE User SET userName = ? where userId = ?");
      $qu->bindValue(1, $user, PDO::PARAM_STR);
      $qu->bindValue(2, $userId, PDO::PARAM_INT);
      $qu->execute();

      $candidates = explode('-', $candidates);
      foreach ($candidates as $i => $candidate) {
  
        $qc = $db->prepare("SELECT candidateId from Candidate WHERE candidate = ? AND scheduleId = ?");
        $qc->bindValue(1, $candidate, PDO::PARAM_STR);
        $qc->bindValue(2, $scheduleId, PDO::PARAM_STR);
        $qc->execute();
        $candidateId = $qc->fetch()->candidateId;
  
        //  availabilityの更新
        $qa = $db->prepare("UPDATE Availability SET availability = ? WHERE userId = ? AND candidateId = ?");
        $qa->bindValue(1, $availabilities[$i], PDO::PARAM_INT);
        $qa->bindValue(2, $userId, PDO::PARAM_INT);
        $qa->bindValue(3, $candidateId, PDO::PARAM_INT);
        $qa->execute();
      }
    } else {
      //ユーザーの登録
      $q = $db->prepare("INSERT INTO User (userName, scheduleId) VALUES (?, ?)");
      $q->bindValue(1, $user, PDO::PARAM_STR);
      $q->bindValue(2, $scheduleId, PDO::PARAM_STR);
      $q->execute();
  
      // 登録したユーザーのIDを取得
      $userId = $db->lastInsertId();
  
      $candidates = explode('-', $candidates);
      foreach ($candidates as $i => $candidate) {
  
        $qc = $db->prepare("SELECT candidateId from Candidate where candidate = ? AND scheduleId = ?");
        $qc->bindValue(1, $candidate, PDO::PARAM_STR);
        $qc->bindValue(2, $scheduleId, PDO::PARAM_STR);
        $qc->execute();
        $candidateId = $qc->fetch()->candidateId;
  
        //  availabilityの登録
        $qa = $db->prepare("INSERT INTO Availability (availability, scheduleId, userId, candidateId) VALUES (?, ?, ?, ?)");
        $qa->bindValue(1, $availabilities[$i], PDO::PARAM_INT);
        $qa->bindValue(2, $scheduleId, PDO::PARAM_STR);
        $qa->bindValue(3, $userId, PDO::PARAM_INT);
        $qa->bindValue(4, $candidateId, PDO::PARAM_INT);
        $qa->execute();
      }

    }

  }

?>