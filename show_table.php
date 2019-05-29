<?php
// index.phpですでにrequireしているから、もうrequireする必要ない？
  // require 'database.php';
  function show_table($scheduleId) {  
    $weeks = ['日', '月', '火', '水', '木', '金', '土'];
    $symbols = array('○', '△', '×');
    list($circle, $triangle, $cross) = array(0, 1, 2);
    //スケジュール名、候補日、ユーザー、候補日（二重の連想配列）をとってきて返す。
    //schedule_name: string, candidates: array, users: array, availabilities: associative array(associative array)
    list($schedule_name, $candidates, $users, $availabilities) = get_schedule($scheduleId);
    
    ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <title>Scheduler</title>
  </head>
  <body>
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

      $html = "<table><tr><td>日程</td><td>";
      $html .= implode('</td><td>', $symbols);
      $html .= '</td>';
      //issetだと空の配列もtrueになるっぽい
      if (! empty($users)) {
        $html .= '<td>';
        $html .= implode('</td><td>', $users);
        $html .= '</td>';
      }

      if (isset($candidates)) {
        $html .= '</tr><tr>';
        foreach ($candidates as $candidate) {
          
          $time = strtotime($candidate);
          $html = $html . '<td>' . date('n/j', $time) . '(' . $weeks[date('w', $time)] .')</td><td>';
          // scheduleIdとcandidateからとってくる。
          $symbol_sum = get_symbol_sum($scheduleId, $candidate);
  
          $html .= implode('</td><td>', $symbol_sum);
          $html .= '</td>';
          foreach ($users as $user) {
            $html = $html . '<td>' . $symbols[$availabilities[$user][$candidate]] . '</td>';
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
          <input name="user" required>
        </div>
        <div>
          <p>候補</p>
          <?php
            if (isset($candidates)) {
              $cand_array = [];
              $avail_array = [];
              $html = '<table>';
              foreach ($candidates as $ci => $candidate) {
                array_push($cand_array, $candidate);
                //初期では丸にする。
                array_push($avail_array, $circle);
                $time = strtotime($candidate);
                $html .= '<tr><td>' . date('n/j', $time) . '(' . $weeks[date('w', $time)] . ')</td>';
                foreach ($symbols as $sj => $symbol) {
                  if ($sj == 0) {
                    //初期では丸にする。
                    $html .= "<td class=\"btn selected\" data-index=\"{$ci}-{$sj}\">" . $symbol . '</td>';
                  } else {
                    $html .= "<td class=\"btn\" data-index=\"{$ci}-{$sj}\">" . $symbol . '</td>';
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
          ?>
        </div>
        <div>
          <input type="submit" value="とうろく">
        </div>
      </form>
    </div>
    <script src="../javascript/show_table.js"></script>
  </body>
</html>
<?php } ?>