<?php

if($app->msg){
  if(is_array($app->msg)){
    foreach($app->msg as $m){
      $m = json_decode($m);
      echo parseReturnMessage($m);
    }
  } elseif (is_string($app->msg)) {
    $app->msg = json_decode($app->msg);
    echo parseReturnMessage($app->msg);
  } 
}

?>