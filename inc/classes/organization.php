<?php class organization {

  public $id;
  public $name;
  public $created;
  public $public;
  public $since;
  public $link;
  public $publicStatus;

  public $full = false;
  public $members = false;
  public $teams = false;
  public $abilities = false;

  public function __construct($org=null, $full=false){
    $this->full = $full;
    if($org){
      $org = $this->getOrg($org);
      if($this->full){
        $org->members = $this->getOrgRoster($org->id);
        $org->teams = $this->getOrgTeams($org->id);
        $ability = new ability();
        $org->abilities = $ability->getOrgAbilities($org->id);
      }
      $org = $this->parseOrg($org);
      foreach ($org as $k => $v){
        $this->$k = $v;
      }
      return $org;
    }
  }

  public function parseOrg(&$org){
    $org->since = date('F Y',strtotime($org->created));
    $org->link = "<a class='link blue dim' ";
    $org->link.= "href='?action=viewOrg&org=$org->id'>$org->name</a>";

    if ($org->public) {
      $org->publicStatus = 'Public';
    } else {
      $org->publicStatus = 'Not public';
    }
    $org->membercount = singular($org->membercount, "Member", "Members");

    return $org;
  }

  public function parseRelation(&$member) {
    switch ($member->relation){
      case 'R':
        $member->relationStatus = "Application pending";
        $member->relationIcon = "<i class='fa fa-hourglass'></i>";
        $member->relationLink = " ".btn("Deny","denyMembership&user=$member->id&org=$member->org",FALSE).btn("Approve","approveMembership&user=$member->id&org=$member->org",1);
        $member->relationClass = 'washed-yellow';
      break;

      case 'M':
        $member->relationStatus = "Member";
        $member->relationIcon = "<i class='fa fa-user'></i>";
        $member->relationClass = 'washed-green';
        $member->relationLink = " ".btn("Remove","cancelMembership&user=$member->id&org=$member->org",FALSE);
        $member->relationLink.= " ".btn("Promote to leader","promoteOrgLeader&user=$member->id&org=$member->org",TRUE);
      break;

      case 'L':
        $member->relationStatus = "Organization Leader";
        $member->relationIcon = "<i class='fa fa-user-circle'></i>";
        $member->relationClass = 'lightest-blue';
        $member->relationLink = " ".btn("Demote","demoteOrgLeader&user=$member->id&org=$member->org",FALSE);
      break;
    }
    $member->relationAge = date('F, Y',strtotime($member->timestamp));
    $member->orgLink = "<a href='?action=viewOrgMember&org=$member->org";
    $member->orgLink.= "&member=$member->id'>View</a>";
    return $member;
  }

  public function parseTeam(&$team){
    $team->since = date('F Y',strtotime($team->created));

    $team->link = "<a href='?action=manageTeams&team=$team->id'>";
    $team->link.= "$team->name</a>";
    $team->members = array();
    $team->membercount = "TBD";

    return $team;
  }

  public function getOrg($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_organization.*,
      count(DISTINCT tbl_org_members.user) AS membercount
      FROM tbl_organization
      LEFT JOIN tbl_org_members ON tbl_organization.id = tbl_org_members.org AND tbl_org_members.relation != 'R'
      WHERE tbl_organization.id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getOrganizations(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_organization.*,
      IF (count(DISTINCT tbl_org_members.user) > 0, count(DISTINCT tbl_org_members.user), 0) AS membercount
      FROM tbl_organization
      LEFT JOIN tbl_org_members ON tbl_organization.id = tbl_org_members.org AND tbl_org_members.relation != 'R'
      GROUP BY tbl_organization.id;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($orgs = $db->resultset() as &$org){
      $org = $this->parseOrg($org);
    }
    return $orgs;
  }

  public function newOrganization($name=null){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to create an organization");
    }
    if (!$name){
      return returnError("Organizations must have a name!");
    }
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_organization (name, created)
      VALUES (?, NOW());");
    $db->bind(1, $name);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("$name has been added as a new organization!");
  }

  //Returns public organizations that this user is not a member of as well
  public function getUserOrganizations($user){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_organization.*,
      membership.*,
      count(DISTINCT membership.user) AS membercount
      FROM tbl_organization
      LEFT JOIN tbl_org_members AS membership ON tbl_organization.id = membership.org
      WHERE membership.user = ? AND tbl_organization.public = 0
      OR membership.user = ? OR membership.user IS NULL AND tbl_organization.public = 1;");
    $db->bind(1, $user);
    $db->bind(2, $user);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($orgs = $db->resultset() as &$org){
      $org = $this->parseOrg($org);
    }
    return $orgs;
  }

  public function getUsersNotInOrg($org){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT bg_user.id,
      bg_user.username,
      bg_user.rank,
      bg_user.created,
      bg_user.status
      FROM bg_user
      LEFT JOIN bg_org_members ON bg_org_members.user = bg_user.id
      WHERE bg_org_members.user IS NULL
      AND bg_org_members.org IS NULL OR bg_org_members.org != ?");
    $db->bind(1, $org);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $u = new user();
    foreach ($users = $db->resultset() as &$user){
      $user = $u->parseUser($user);
    }
    return $users;
  }

  public function flipOrgPublic(){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to modify this organization");
    }
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    if ($this->public){
      $flag = 0; //Make it private
    } else {
      $flag = 1; //Make it public
    }
    $db->query("UPDATE tbl_organization SET public = ? WHERE id = ?");
    $db->bind(1, $flag);
    $db->bind(2, $this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if ($flag) {
      return returnSuccess("$this->name is now public");
    } else {
      return returnSuccess("$this->name is now private");
    }
  }

  public function getOrgRoster($org){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_org_members.*,
      members.id,
      members.username,
      members.rank,
      members.created,
      members.status
      FROM tbl_org_members
      LEFT JOIN tbl_user AS members ON tbl_org_members.user = members.id
      WHERE tbl_org_members.org = ?");
    $db->bind(1,$org);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $user = new user();
    foreach ($roster = $db->resultset() as &$member){
      $member = $user->parseUser($member);
      $member = $this->parseRelation($member);
    }
    return $roster;
  }

  public function getOrgTeams($org) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_team WHERE org = ?");
    $db->bind(1, $org);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $ability = new ability();
    foreach ($teams = $db->resultset() as &$team){
      $team = $this->parseTeam($team);
      $team->requirements = $ability->getTeamRequirements($team->id);
    }
    return $teams;
  }

  public function getTeam($team) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_team WHERE id = ?");
    $db->bind(1, $team);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $team = $db->single();
    $ability = new ability();
    $team->requirements = $ability->getTeamRequirements($team->id);
    return $this->parseTeam($team);
  }

  public function addTeam($team){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to modify this organization");
    }
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_team (name, org, created)
      VALUES (?, ?, NOW());");
    $db->bind(1,$team);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Team $team has been created in $this->name");
  }

  public function manageTeamRequirements($team, $abilities){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to modify this organization");
    }
    if(!is_array($abilities)) return returnError("Invalid data!");
    $team = $this->getTeam($team);
    if(!$team) return returnError("Unable to find team");

    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT IGNORE INTO tbl_team_ability (team, ability, created)
      VALUES (?, ?, NOW())");
    $db->bind(1, $team->id);
    foreach ($abilities as $a){
      $a = str_replace('require-', '', $a);
      $db->bind(2, $a);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
    }
    return returnSuccess("Requirements for $team->name have been updated.");
  }

  public function addTeamRequirement($team, $ability){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to modify this organization");
    }
    $team = $this->getTeam($team);
    if(!$team) return returnError("Unable to find team");

    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT IGNORE INTO tbl_team_ability (team, ability, created)
      VALUES(?, ?, NOW())");
    $db->bind(1, $team->id);
    $db->bind(2, $ability);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Requirements for $team->name have been updated.");
  }

  public function deleteTeamRequirement($team, $ability){
    $user = new user();
    if(!$user->isSuperAdmin()){
      return returnError("You do not have sufficient privileges to modify this organization");
    }
    $team = $this->getTeam($team);
    if(!$team) return returnError("Unable to find team");

    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("DELETE FROM tbl_team_ability WHERE team = ? AND ability = ?");
    $db->bind(1, $team->id);
    $db->bind(2, $ability);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Requirements for $team->name have been updated.");
  }

}