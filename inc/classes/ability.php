<?php

class ability {

  public $full = false;
  public $id;
  public $org;
  public $name;
  public $icon;
  public $color;
  public $created;

  public $since;
  public $link;
  public $html;

  public function __construct($ability=null,$full=null) {
    $this->full = $full;
    if ($ability){
      $ability = $this->getAbility($ability,$this->full);
      foreach ($ability as $k => $v){
        $this->$k = $v;
      }
      return $ability;
    }
  }

  public function parseAbility(&$ability) {
    $ability->since = date('F, Y',strtotime($ability->created));

    $ability->link = "?action=manageAbilities&ability=$ability->id";

    $ability->html = "<a class='dib ba pa2 b br2 f5 $ability->color link ";
    $ability->html.= "hover-bg-$ability->color pointer hover-white' ";
    $ability->html.= "href='$ability->link'";
    $ability->html.= "><i class='fa fa-fw fa-$ability->icon' ";
    $ability->html.= "title='$ability->name'></i> $ability->name</a>"; 

    return $ability;
  }

  public function getAbility($ability,$full){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_ability WHERE id = ?");
    $db->bind(1, $ability);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $ability = $db->single();
    if ($full){
      $ability->bearers = $this->getAbilityBearers($ability->id);
    }
    return $this->parseAbility($ability);
  }

  public function addNewAbility($org, $name, $icon, $color){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_ability (org, name, icon, color, created)
      VALUES (?, ?, ?, ?, NOW())");
    $db->bind(1, $org);
    $db->bind(2, $name);
    $db->bind(3, $icon);
    $db->bind(4, $color);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("The $name ability has been created");
  }

  public function getOrgAbilities($org){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_ability WHERE org = ?");
    $db->bind(1, $org);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($abilities = $db->resultSet() as &$a){
      $a = $this->parseAbility($a);
    }
    return $abilities;
  }

  public function getTeamRequirements($team){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM bg_ability
      LEFT JOIN bg_team_ability ON bg_team_ability.ability = bg_ability.id
      WHERE bg_team_ability.team = ?;");
    $db->bind(1, $team);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($abilities = $db->resultSet() as &$a){
      $a = $this->parseAbility($a);
    }
    return $abilities;
  }

  public function getAbilityBearers($ability){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_user.id, tbl_user.username, tbl_user.rank,
      tbl_user.status, tbl_user.created FROM tbl_member_ability
      LEFT JOIN tbl_user ON tbl_member_ability.user = tbl_user.id
      WHERE tbl_member_ability.ability = ?;");
    $db->bind(1, $ability);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $user = new user(FALSE, FALSE);
    foreach ($users = $db->resultSet() as &$u){
      $u = $user->parseUser($u);
    }
    return $users;
  }
}