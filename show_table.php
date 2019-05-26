<?php
// index.phpですでにrequireしているから、もうrequireする必要ない？
  // require 'database.php';
  function show_table($id) {  
    list($circle, $triangle, $cross) = array(0, 1, 2);
    $symbols = array('○', '△', '×');
    //スケジュール名、候補日、ユーザー、候補日（二重の連想配列）をとってきて返す。
    //schedule_name: string, candidates: array, users: array, availabilities: associative array(associative array)
    list($schedule_name, $candidates, $users, $availabilities) = get_schedule($id);
    
    ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css//style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <title>Scheduler</title>
  </head>
  <body>
    <div>
      <h2><?= $schedule_name ?></h2>
    </div>
    <div>
      <p>候補</p>
      <?php 

      $html = "<table><tr><td>日程</td><td>";
      $html .= implode('</td><td>', $symbols);
      $html .= '</td>';
      if (isset($users)) {
        $html .= '<td>';
        $html .= implode('</td><td>', $users);
        $html .= '</td>';
      }

      if (isset($candidates)) {
        $html .= '</tr><tr>';
        foreach ($candidates as $candidate) {
          $html = $html . '<td>' . $candidate . '</td><td>';
          // いい感じの名前に。。。
          // $avail_sum = get_sum($id, $candidate);
          //データベースから合計とってくる、とりあえず仮で適当な配列
          $avail_sum = array(2, 1, 1);
          $html .= implode('</td><td>', $avail_sum);
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
              $input_array = [];
              $html = '<table>';
              foreach ($candidates as $ci => $candidate) {
                array_push($input_array, $circle);
                $html .= '<tr><td>' . $candidate . '</td>';
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
            $value_attr = implode('-', $input_array);
            print "<input id=\"availabilities\" class=\"hidden\" type=\"text\" name=\"availability\" value=\"{$value_attr}\">";
          ?>
        </div>
        <div>
          <input type="submit" value="とうろく">
        </div>
      </form>
    </div>
    <script src="./javascript/show_table.js"></script>
  </body>
</html>
<?php } ?>