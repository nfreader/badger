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
      $bg = 'washed-red';
      $color = 'dark-red';
      $icon = 'times';
      $state = 'Error';
    break;

    case 1:
      $bg = 'washed-green';
      $color = 'dark-green';
      $icon = 'check';
      $state = 'Success';
    break;

    case 2:
    default:
      $bg = 'light-blue';
      $color = 'dark-blue';
      $icon = 'exclamation';
      $state = 'Message';
    break;
  }

  $html ="<div class='pa3 bg-$bg $color ba b--$color mb2'>";
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

function btn($text, $query, $type=null, $class=null){
  switch($type){
    default:
    case 'neutral':
    case 2:
      $fore = "dark-blue";
      $back = "light-blue";
      $icon = "";
    break;

    case 0:
    case 'negative':
    case FALSE:
      $fore = "dark-red";
      $back = "washed-red";
      $icon = "<i class='fa fa-times'></i>";
    break;

    case 1:
    case 'positive':
      $fore = "dark-green";
      $back = "light-green";
      $icon = "<i class='fa fa-check'></i>";
    break;
  }
  $html = "<a class='btn link dim bg-$back $fore ba b--$fore ph2 dib mr3 pv1 br2 v-mid $class'";
  $html.= " title='$text' href='?action=$query'>$icon $text</a>";
  return $html;
}

/* Singular
 *
 * Based on the input, outputs the singular or plural of the specified unit
 *
 * @param $value (int) The value we're looking at
 * @param $one (string) The output if the value is one
 * @param $many (string) The output if the value is greater than one
 *
 * @return string
 *
 */

function singular($value, $one, $many) {
  if ($value == 1) {
    return number_format($value)." $one";
  } else {
    return number_format($value)." $many";
  }
}