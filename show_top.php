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
      <div id="left-side" class="col-12 col-md-6">
        <div>
          <h2 class="mt-5 mb-5"><a href="/">Scheduler</a></h2>
        </div>
        <?php print "<form method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">"?>
          <div>
            <h5>予定名</h5>
            <input class="form-control" name="schedule-name" required>
          </div>
          <div>
            <h5 class="mt-4">候補日程</h5>
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
      <div id="c-container" class="col-12 col-md-6 mb-5">
        <div id="calendar">
        </div>
      </div>
    </div>
  </div>
  <script src="./javascript/calendar.js"></script>
</body>
</html>
<?php }

  function invalid_page() {   ?>
    
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
      <h3 class="mt-5 mx-auto">This is invalid access.</h3>
    </div>
    <div class="row">
      <h5 class="mt-3 mx-auto">Please confirm URL.</h5>
    </div>
  </div>
  <script src="./javascript/calendar.js"></script>
</body>
</html>
<?php } ?>