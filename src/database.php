<?php

function getDbConn() {
  return pg_connect($_ENV["DATABASE_URL"]);
}

function validateUser($username, $passwordGiven) {
  // output("Validating user $username $passwordGiven");
  $conn  = getDbConn();
  $query = "SELECT id, passwordhash FROM users WHERE username = $1";
  $result = pg_query_params($conn, $query, [ $username ]);
  $arr = pg_fetch_array($result, 0, PGSQL_NUM);
  if (pg_num_rows($result) == 1) {
    $id = intval($arr[0]);
    $passwordHash = $arr[1];
    // var_dump($arr);
    $conclusion = password_verify($passwordGiven, $passwordHash);
    // var_dump($conclusion);
    if ($conclusion) {
      return [
        "id" => $id,
        "username" => $username
      ];
    }
  }
  return null;
}

function createUser($username, $passwordGiven) {
  $conn  = getDbConn();
  $passwordHash = password_hash($passwordGiven, PASSWORD_BCRYPT, [ "cost" => 10 ]);
  $query = "INSERT INTO users (username, passwordhash) VALUES ($1, $2)";
  $result = pg_query_params($conn, $query, [ $username, $passwordHash ]);
  return !!$result;
}
