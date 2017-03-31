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

    case 'applyToOrg':
      $app->setmsg($user->applyToOrg($_GET['org']));
      $app->setinclude('organization');
    break;

    case 'approveMembership':
      $app->setmsg($user->approveOrgMembership($_GET['user'],$_GET['org']));
      $app->setinclude('orgMember');
    break;

    case 'cancelMembership':
      $app->setmsg($user->removeOrgMembership($_GET['user'],$_GET['org']));
      $app->setinclude('organization');
    break;

    case 'denyMembership':
      $app->setmsg($user->removeOrgMembership($_GET['user'],$_GET['org']));
      $app->setinclude('organization');
    break;

    case 'promoteOrgLeader':
      $app->setmsg($user->promoteToLeader($_GET['user'],$_GET['org']));
      $app->setinclude('orgMember');
    break;

    case 'demoteOrgLeader':
      $app->setmsg($user->demoteOrgLeader($_GET['user'],$_GET['org']));
      $app->setinclude('orgMember');
    break;

    case 'addMember':
      $app->setInclude('addMember');
    break;

    case 'addUserToOrg':
      $app->setmsg($user->addMemberToOrg($_GET['user'],$_GET['org']));
      $app->setInclude('addMember');
    break;

    case 'me':
      $app->setinclude('me');
    break;

    case 'viewOrgMember':
      $app->setinclude('orgMember');
    break;

    case 'manageTeams':
      $app->setinclude('manageTeams');
    break;

    case 'manageAbilities':
      $app->setinclude('abilities');
    break;

    case 'newAbility':
      $ability = new ability();
      $app->setmsg($ability->addNewAbility($_GET['org'],
        $_POST['name'],
        $_POST['icon'],
        $_POST['color']));
      $app->setInclude('abilities');
    break;

    case 'newTeam':
      $org = new organization($_GET['org']);
      $app->setmsg($org->addTeam($_POST['name']));
      $app->setInclude('manageTeams');
    break;

    case 'manageTeamAbilities':
      $org = new organization();
      $team = $org->getTeam($_GET['team']);
      $app->setmsg($org->manageTeamRequirements($team->id, $_POST));
      $app->setInclude('manageTeams');
    break;

    case 'addTeamRequirement':
      $org = new organization();
      $team = $org->getTeam($_GET['team']);
      $app->setmsg($org->addTeamRequirement($team->id, $_GET['ability']));
      $app->setInclude('manageTeams');
    break;

    case 'removeTeamRequirement':
      $org = new organization();
      $team = $org->getTeam($_GET['team']);
      $app->setmsg($org->deleteTeamRequirement($team->id, $_GET['ability']));
      $app->setInclude('manageTeams');
    break;
  }
}