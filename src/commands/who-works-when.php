<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// Notice this circumvenes the statements and component grants
// and therefore it only works in adminParty mode:
function whoWorksWhen($context) {
  if ($context['adminParty']) {
    $ret = [];
    $movements = getAllMovements();
    $componentNames = getAllComponentNames();
    for ($i = 0; $i < count($movements); $i++) {
      if ($movements[$i]["type_"] == 'worked') {
        $from = $componentNames[$movements[$i]["fromcomponent"]];
        $to = $componentNames[$movements[$i]["tocomponent"]];
        $date = $movements[$i]["timestamp_"];
        var_dump([$from, $to, $date]);
      }
    }
    // var_dump($componentNames);
    return $ret;
  } else {
      return ["This command disregards access checks so it only works in admin party mode"];
  }
}