<?php
require_once("../utils.php");

// ...
$journal = readJournal($argv[1]);
$config = readJson($argv[3]);
$report = readJson($argv[4]);
var_dump($journal);
var_dump($config);
var_dump($report);