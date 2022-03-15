<?php
  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
  } else {
    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
  }
  echo "hello world";
  $conn  = pg_connect($_ENV["DATABASE_URL"]);
  $username = pg_escape_string($_SERVER['PHP_AUTH_USER']);
  $pwhash = pg_escape_string(password_hash($_SERVER['PHP_AUTH_PW'], PASSWORD_BCRYPT, [ 'cost' => 10 ]));
  echo $username;
  echo $pwhash;
  $query = "SELECT pwhashbcryptcost10 FROM users WHERE username = '${username}'";
  echo $query;
  $result = pg_query($conn, $query);
  echo "result:";
  var_dump($result);
  echo pg_num_rows($result);
  $arr = pg_fetch_array($result, 0, PGSQL_NUM);
  echo "array:";
  var_dump($arr);
?>
