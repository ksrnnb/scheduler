<?php
  function show_top_page() {
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <title>Scheduler</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
  <div class="container">
      <div class="row">
      <div id="left-side" class="col-6">
        <div>
          <h2 class="mt-5 mb-5"><a href="/">Scheduler</a></h2>
        </div>
        <?php print "<form method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">"?>
          <div>
            <h4>予定名</h4>
            <input class="form-control" name="schedule-name" required>
          </div>
          <div>
            <h4 class="mt-3">候補日程</h4>
            <!-- <textarea id="candidates" name="candidates" rows=10 required readonly></textarea> -->
            <textarea id="candidates" class="form-control" rows=10 required readonly></textarea>
            <input id="candidates_input" type="hidden" name="candidates">
          </div>
          <div>
            <input class="mt-4 btn btn-primary" type="submit" value="予定をつくる">
          </div>
        </form>
      </div>
      <!-- js -->
      <div id="c-container" class="col-6">
        <div id="calendar">
        </div>
      </div>
    </div>
  </div>
  <script src="./javascript/calendar.js"></script>
</body>
</html>
<?php } ?>