<?php

function timestampToDateTime($timestamp) {
  $ret = new DateTime();
  $ret->setTimestamp($timestamp);
  return $ret->format('Y-m-d H:i:s');
}
