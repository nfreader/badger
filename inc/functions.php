<?php

function returnError($msg) {
  return json_encode(array('message'=>$msg,'level'=>0));
}

function returnMessage($msg) {
  return json_encode(array('message'=>$msg,'level'=>2));
}

function returnSuccess($msg) {
return json_encode(array('message'=>$msg,'level'=>1));
}

function alert($msg, $level=2) {
  $data = new stdClass;
  $data->level = $level;
  $data->message = $msg;
  return parseReturnMessage($data);
}

function parseReturnMessage($msg){
  switch ($msg->level){
    case 0:
      $bg = 'bg-washed-red';
      $color = 'dark-red';
      $icon = 'times';
      $state = 'Error';
    break;

    case 1:
      $bg = 'bg-washed-green';
      $color = 'dark-green';
      $icon = 'check';
      $state = 'Success';
    break;

    case 2:
    default:
      $bg = 'bg-light-blue';
      $color = 'dark-blue';
      $icon = 'exclamation';
      $state = 'Message';
    break;
  }

  $html ="<div class='pa3 $bg $color ba b--$color mb2'>";
  $html.= "<i class='fa fa-$icon fa-fw' aria-hidden='true'"; 
  $html.= "title='$state'></i> $msg->message</div>";
  return $html;
}

function testResult($returned, $expected, $name){
  if (json_encode($returned) === json_encode($expected)) {
    $return = returnSuccess("$name passes!");
  } else {
    $return = returnError("$name fails.");
    var_dump($expected);
    var_dump($returned);
  }
  $html = "<strong class='db mb1'>$name</strong>";
  $html.= parseReturnMessage(json_decode($return));
  return $html;
}
