<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <title>Scheduler</title>
</head>
<body>
  <div>
    <h2>Scheduler</h2>
  </div>
  <div class="container">
    <form method="POST" action="">
      <div>
        <p>予定名</p>
        <input name="title">
      </div>
      <div>
        <p>候補日程</p>
        <textarea name="candidate"></textarea>
      </div>
      <div>
        <input type="submit" value="予定をつくる">
      </div>
    </form>
    <div id="calendar">
        <!-- js -->
    </div>
  </div>
  <script src="calendar.js"></script>
</body>
</html>