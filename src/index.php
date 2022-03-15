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
  $username = $_SERVER['PHP_AUTH_USER'];
  $pwhash = password_hash($_SERVER['PHP_AUTH_PW'], PASSWORD_BCRYPT, [ 'cost' => 10 ]);
  $result = pg_query_params($conn, 'SELECT * FROM users WHERE username = $1 AND pwhashbcryptcost10 = $2',
    array($username, $pwhash));
  echo "result:";
  var_dump($result);

  $arr = pg_fetch_array($result, 0, PGSQL_NUM);
  echo "array:"
  var_dump($arr);
?>
