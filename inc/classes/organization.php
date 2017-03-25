<?php class organization {

  public function __construct($org=null, $full=false){
    if($org){
      $org = $this->getOrg($org);
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
    return $org;
  }

  public function getOrg($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_organization WHERE id = ?");
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
    $db->query("SELECT * FROM tbl_organization");
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

}