<?php
  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
  }
  $conn  = pg_connect($_ENV["DATABASE_URL"]);
  $username = pg_escape_string($conn, $_SERVER['PHP_AUTH_USER']);
  
  $passwordGiven = $_SERVER['PHP_AUTH_PW'];

  $query = "SELECT pwhashbcryptcost10 FROM users WHERE username = '${username}'";
  $result = pg_query($conn, $query);
  $arr = pg_fetch_array($result, 0, PGSQL_NUM);
  $hash = $arr[0];
  $conclusion = password_verify($passwordGiven, $hash);
  echo "conclusion:";
  var_dump($conclusion);
?>
