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
    <title>Scheduler</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <div>
        <h2><a href="/">Scheduler</a></h2>
      </div>
      <div>
        <h2><?= $schedule_name ?></h2>
      </div>
      <div>
        <h3>
        <?php
          $url = '';
          if (empty($_SERVER['HTTPS'])) {
            $url .= 'http://';
          } else {
            $url .= 'https://';
          }
            $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            
            print $url;
        ?>
        </h3>
      </div>
      <div>
        <p>候補</p>
        <?php 

        $html = '<table id="schedule" class="table table-bordered"><thead><tr><th scope="col">日程</th><th>';
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
              // var_dump($availabilities[$userId]);
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
        <button id="input">入力ボタン</button>
      </div>
      <div id="form" class="hidden">
        <?php print "<form method=\"POST\" action=\"/registration.php\">"?>
          <div>
            <p>ユーザ名</p>
            <input name="user" id="user-form" required>
          </div>
          <div>
            <p>候補</p>
            <?php
              if (isset($candidates)) {
                $cand_array = [];
                $avail_array = [];
                $html = '<table class="table" id="availability-form">';
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
            <input type="submit" id="submit_button" value="とうろく">
          </div>
        </form>
      </div>
    </div>
    <script src="../javascript/show_table.js"></script>
  </body>
</html>
<?php } ?>