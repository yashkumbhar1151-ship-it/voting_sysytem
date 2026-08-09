<?php
  header('Content-Type: application/json');

  $host = getenv('MYSQLHOST') ?: 'localhost';
  $connect = @mysqli_connect($host, getenv('MYSQLUSER'), getenv('MYSQLPASSWORD'), getenv('MYSQLDATABASE'), getenv('MYSQLPORT') ?: 3306);

  if (!$connect) {
    echo json_encode(['status' => -1, 'winner' => null]);
    exit;
  }

  $response = ['status' => 0, 'winner' => null];

  $e = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  if ($e && mysqli_num_rows($e) > 0) {
    $ed = mysqli_fetch_array($e);
    $response['status'] = (int)$ed['status'];

    if ($response['status'] == 2) {
      $c = mysqli_query($connect, "SELECT * FROM user WHERE role=2 ORDER BY vote DESC LIMIT 1");
      if ($c && mysqli_num_rows($c) > 0) {
        $w = mysqli_fetch_array($c);
        if ((int)$w['vote'] > 0) {
          $response['winner'] = [
            'name' => $w['name'],
            'votes' => (int)$w['vote'],
            'branch' => $w['branch'],
            'photo' => $w['photo']
          ];
        }
      }
    }
  }

  mysqli_close($connect);
  echo json_encode($response);
?>
