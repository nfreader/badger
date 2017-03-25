<?php

//Available classes:
// app()
// user()
$user = new user();
if(!$user->online){ //Guest actions
  switch($app->action){
    case 'register':
      $user = new user();
      $app->setmsg($user->register(
        $_POST['username'],
        $_POST['password'],
        $_POST['password2'],
        $_POST['email']));
      $app->setinclude('home');
    break;

    case 'login':
      $user = new user();
      $app->setmsg($user->login(
        $_POST['username'],
        $_POST['password']));
      $app->setinclude('home');
    break;

    case 'test':
    $app->setmsg($app->logEvent('TST','Testing Event Log!'));
    $app->setinclude('guest');
    break;

  }
} else { //Actions for logged in users
  switch($app->action){
    case 'logout':
      $app->setmsg($user->logout());
      $app->setinclude('guest');
    break;

    case 'newOrg':
      $org = new organization();
      $app->setmsg($org->newOrganization($_POST['organization']));
      $app->setinclude('home');
    break;

    case 'viewOrg':
      $app->setinclude('organization');
    break;

    case 'userMetaFields':
      $app->setinclude('userMetaFields');
    break;

    case 'newUserMetaField':
      $app->setmsg($app->newUserMetaField($_POST['name']));
      $app->setinclude('userMetaFields');
    break;

    case 'updateUserMeta':
      $app->setmsg($user->updateUserMeta($_GET['field'], $_POST['value']));
      $app->setinclude('me');
    break;

    case 'flipOrgPublic':
      $org = new organization($_GET['org']);
      $app->setmsg($org->flipOrgPublic());
      $app->setinclude('organization');
    break;

    case 'me':
      $app->setinclude('me');
    break;
  }
}