<?php class app {

  public $app_URL = APP_URL;
  public $app_ROOT = ROOTPATH;

  public $include = 'home';
  public $msg = null;
  public $action = null;

  public function setmsg($msg){
    $this->msg = $msg;
  }

  public function setinclude($include){
    $this->include = $include;
  }
  
  public function logEvent($code='GEN', $message=null){
    $user = new user();
    $db = new database();
    $db->query("INSERT INTO tbl_log
      (who, code, message, `from`, `timestamp`)
      VALUES (?, ?, ?, ?, NOW())");
    $db->bind(1, $user->id);
    $db->bind(2, $code);
    $db->bind(3, $message);
    $db->bind(4, ip2long($_SERVER['REMOTE_ADDR']));
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return true;
  }

  public function getUserMetaFields(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT tbl_meta_fields.*,
      count(distinct members.user) as members
      FROM tbl_meta_fields
      LEFT JOIN tbl_user_meta AS members ON tbl_meta_fields.id = members.field 
      GROUP BY tbl_meta_fields.id");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function newUserMetaField($name){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_meta_fields (name, created)
      VALUES (?, NOW());");
    $db->bind(1, $name);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("$name has been added as a user meta field.");
  }
}