<?php
// index.phpですでにrequireしているから、もうrequireする必要ない？
  // require 'database.php';
  function show_table($scheduleId) {  
    $weeks = ['日', '月', '火', '水', '木', '金', '土'];
    $symbols = array('○', '△', '×');
    list($circle, $triangle, $cross) = array(0, 1, 2);
    //スケジュール名、候補日、ユーザー、候補日（二重の連想配列）をとってきて返す。
    //schedule_name: string, candidates: array, users: array(userId => userName)
    // availabilities: associative array (user => array(candidate => availability))
    list($schedule_name, $candidates, $users, $availabilities) = get_schedule($scheduleId);
    
    ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <title>Scheduler</title>

  </head>
  <body>
    <div class="container">
      <div>
        <h2 class="mt-5 mb-5"><a href="/">Scheduler</a></h2>
      </div>
      <div>
        <h2><?= $schedule_name ?></h2>
        <hr color="#ccc">
      </div>
      <h4 class="mt-5">URL</h4>
      <div class="input-group flex-nowrap">
        <input type="text" class="form-control" id="url" aria-label="Username" aria-describedby="addon-wrapping" readonly
        <?php
          $url = '';
          if (empty($_SERVER['HTTPS'])) {
            $url .= 'http://';
          } else {
            $url .= 'https://';
          }
            $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            
            print 'value="' . $url . '"';
        ?>
        >
      </div>
      <div>
        <h4 class="mt-5">候補</h4>
        <?php 

        $html = '<table id="schedule" class="table table-bordered"><thead><tr><th scope="col">日程</th><th scope="col">';
        $html .= implode('</th><th scope="col">', $symbols);
        $html .= '</th>';
        //issetだと空の配列もtrueになるっぽい
        if (! empty($users)) {
          foreach ($users as $userId => $userName) {
            $html .= "<th scope=\"col\"><a href=\"#form\" class=\"user\" data-id={$userId}>{$userName}</a></th>";
          }
        }
        $html .= '</thead>';
        

        if (isset($candidates)) {

          foreach ($candidates as $candidate) {
            
            $time = strtotime($candidate);
            $html = $html . '<tr class="candidate"><td>' . date('n/j', $time) . '(' . $weeks[date('w', $time)] .')</td><td>';
            // scheduleIdとcandidateからとってくる。
            $symbol_sum = get_symbol_sum($scheduleId, $candidate);
    
            $html .= implode('</td><td>', $symbol_sum);
            $html .= '</td>';
            foreach ($users as $userId => $userName) {
              $html = $html . '<td>' . $symbols[$availabilities[$userId][$candidate]] . '</td>';
            }
            $html .= '</tr>';

          }
            
        }

        $html .= '</table>';
        print $html;
        
        ?>
      </div>
      <div>
        <button id="input" class="btn btn-primary mt-5 mb-5">新規登録</button>
      </div>
      <div id="form" class="hidden">
        <h4 class="mt-5">出欠の入力</h4>
        <hr color="#ccc">
        <?php print "<form method=\"POST\" action=\"/registration.php\">"?>
          <div>
            <h5 class="mt-5 mb-0">ユーザ名</h5>
            <input name="user" id="user-form" class="col-6" required>
          </div>
          <div>
            <h5 class="mt-5 mb-0">候補</h5>
            <?php
              if (isset($candidates)) {
                $cand_array = [];
                $avail_array = [];
                $html = '<table class="table table-bordered col-6" id="availability-form">';
                foreach ($candidates as $ci => $candidate) {
                  array_push($cand_array, $candidate);
                  //初期では丸にする。
                  array_push($avail_array, $circle);
                  $time = strtotime($candidate);
                  $html .= '<tr><td>' . date('n/j', $time) . '(' . $weeks[date('w', $time)] . ')</td>';
                  foreach ($symbols as $sj => $symbol) {
                    if ($sj == 0) {
                      //初期では丸にする。
                      $html .= "<td class=\"symbol selected\" data-index=\"{$ci}-{$sj}\">" . $symbol . '</td>';
                    } else {
                      $html .= "<td class=\"symbol\" data-index=\"{$ci}-{$sj}\">" . $symbol . '</td>';
                    }
                  }
                  $html .= '</tr>';
                }
                $html .= '</table>';
                print $html;
              }
              $avail_value = implode('-', $avail_array);
              $cand_value = implode('-', $cand_array);

              print "<input id=\"availabilities\" type=\"hidden\" name=\"availability\" value=\"{$avail_value}\">";
              print "<input type=\"hidden\" name=\"scheduleId\" value=\"{$scheduleId}\">";
              print "<input type=\"hidden\" name=\"candidates\" value=\"{$cand_value}\">";
              print "<input id=\"userId\" type=\"hidden\" name=\"userId\">";
            ?>
          </div>
          <div>
            <input type="submit" class="btn btn-primary mt-5 mb-5" id="submit_button" value="登録">
          </div>
        </form>
      </div>
    </div>
    <script src="../javascript/show_table.js"></script>
  </body>
</html>
<?php } ?>