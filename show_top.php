<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <title>Scheduler</title>
</head>
<body>
  <div>
    <h2>Scheduler</h2>
  </div>
  <div class="container">
    <?php print "<form method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">"?>
      <div>
        <p>予定名</p>
        <input name="schedule-name" required>
      </div>
      <div>
        <p>候補日程</p>
        <textarea id="candidates" name="candidates" rows=10 required readonly></textarea>
      </div>
      <div>
        <input type="submit" value="予定をつくる">
      </div>
    </form>
    <!-- js -->
    <div id="c-container">
      <div id="calendar">
      </div>
    </div>
  </div>
  <script src="calendar.js"></script>
</body>
</html>