<?php class user {

  public $id = 0;
  public $online = FALSE;
  public $label = '';
  public $orgs = false;
  public $metadata = false;

  public function __construct($full=FALSE, $id=null){
    if(!$id && isset($_SESSION['id'])){
      $user = $this->getUser($_SESSION['id'], $full);
      foreach ($user as $k => $v){
        $_SESSION[$k] = $v;
      }
      $this->online = TRUE;
    } elseif($id) {
      $user = $this->getUser($id,$full);
    } else {
      return false;
    }
  }

  public function parseUser(&$user){
    //Parse organizations
    if(isset($user->orgs) && $user->orgs[0]->id){
      foreach ($user->orgs as &$o){
        if (!$o->relation){
          $o->userStatus = "Not a member. ".btn("Apply to join?","applyToOrg&org=$o->id",1);
          $o->class = "";
        } else {
          switch ($o->relation){
            case 'R':
              $o->userStatus = "Application Pending";
              $o->class = "washed-yellow";
            break;

            case 'M':
              $o->userStatus = "Active Member";
              $o->class = "washed-green";
            break;

            case 'L':
              $o->userStatus = "Org Leader";
              $o->class = "washed-orange";
            break;
          }
        }
      }
    }

    //Basic user data
    $user->since = date('F Y',strtotime($user->created));

    $user->foreColor = 'white';
    $user->backColor = '';
    $user->icon = '';
    $user->fullRank = 'User';


    switch ($user->rank){
      case 'SA':
        $user->fullRank = 'Superadmin';
        $user->backColor = 'dark-red';
        $user->foreColor = 'white';
        $user->icon = "<i class='fa fa-star'></i>";
      break;

      case 'A':
        $user->fullRank = 'Admin';
        $user->backColor = 'dark-yellow';
        $user->foreColor = 'white';
        $user->icon = "<i class='fa fa-check'></i>";
      break;
    }
    $user->label = "<a class='link dim white f6 ";
    $user->label.= "bg-$user->backColor $user->foreColor ph2 pv1 br2 b' ";
    $user->label.= "href='?action=me' title='User Page'>";
    $user->label.= "$user->icon $user->username</a>";

    return $user;
  }

  public function getUser($id, $full){
    $db = new database();
    $db->query("SELECT id, username, email, rank,
        created, status
      FROM tbl_user
      WHERE id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $user = $db->single();
    if($full){
      $org = new organization();
      $user->orgs = $org->getUserOrganizations($user->id);
      $user->abilities = $this->getUserAbilities($user->id);
      $user->metadata = $this->getUserActiveMetaFields($user->id);
    }
    $user = $this->parseUser($user);
    foreach ($user as $k => $v){
      $this->$k = $v;
    }
    return $user;
  }

  public function getUserAbilities($user){
    $db = new database();
    $db->query("SELECT * FROM tbl_member_ability
      WHERE user = ?");
    $db->bind(1,$user);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function register($username, $password, $password2, $email) {
    $username = filter_var($username,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
    if ('' === empty($username)) {
      return returnError("Username is invalid.");
    }

    if (trim($password) == '') {
      return returnError('Password cannot be empty.');
    }

    if ($password != $password2) {
      return returnError('Passwords do not match!');
    }

    if (trim($email) == '') {
      return returnError('You must specify an email address.');
    }

    if (!$this->isUnique($username,$email)) {
      return returnError('Email address or username already in use.');
    }

    $db = new database();
    $db->query("INSERT INTO tbl_user (
        username,
        password,
        email,
        created
      ) VALUES (
        ?,
        ?,
        ?,
        NOW()
        )");
    $db->bind(1,$username);
    $db->bind(2,password_hash($password,PASSWORD_DEFAULT));
    $db->bind(3,$email);

    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

    $app = new app();
    $app->logEvent("REG","Account registered");

    if (DEBUG) {
      $this->activateUser($this->getIDByUsername($username));
      $return[] = returnSuccess("Your account has been created and activated. Please log in.");
    } else {
      $return[] = returnSuccess("Your account has been created and requires activation. Please check your email for the activation link.");
    }
    if(1 == $db->countRows('tbl_user')) {
      $db->query("SELECT id FROM tbl_user WHERE username = ?");
      $db->bind(1,$username);
      $db->execute();
      $id = $db->single()->id;
      $db->query("UPDATE tbl_user SET status = 1, rank = 'SA'
        WHERE id = ?");
      $db->bind(1,$id);
      $db->execute();
      $return[] = returnSuccess("Initial user detected. You have been promoted to administrator and activated. Please log in now.");
      $app->logEvent("INT","Initial user promoted to admin");
    }
    return $return;
  }

  public function login($username, $password) {
    $db = new database();
    $db->query("SELECT password FROM tbl_user
      WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $user = $db->single();
    if (!$user) {
      return returnError("Incorrect password");
    }
    if(!password_verify($password, $user->password)) {
      return returnError("Incorrect password");
    } else {
      $db->query("SELECT id, username, email, rank,
        created, status
      FROM tbl_user
      WHERE username = :username");
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();
      foreach ($login as $k => $v){
        $_SESSION[$k] = $v;
        $this->$k = $v;
      }
      $this->online = TRUE;
      if ($login->status == 0) {
        return returnMessage("You are now logged in as $login->username. Your account is awaiting activation.");
      } else {
        return returnSuccess("You are now logged in as $login->username.");
      }
      $app = new app();
      $app->logEvent("LI","Logged in");
      return $return;
    }
  }

  public function logout(){
    $app = new app();
    $app->logEvent("LO","Logged out");
    $_SESSION = '';
    session_destroy();
    return returnSuccess("You have logged out.");
  }

  public function isUnique($username, $email) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count
      FROM tbl_user WHERE username = :username OR email = :email");
    $db->bind(':username', $username);
    $db->bind(':email', $email);
    $db->execute();
    if (0 == $db->single()->count) {
      return true;
    } else {
      return false;
    }
  }

  public function activateUser($id) {
    $db = new database();
    $db->query("UPDATE tbl_user SET status = 1 WHERE id = ?");
    $db->bind(1,$id);
    $db->execute();
    $app = new app();
    $app->logEvent("UA","User activated");
  }

  public function getIDByUsername($username){
    $db = new database();
    $db->query("SELECT id FROM tbl_user WHERE username = ?");
    $db->bind(1,$username);
    $db->execute();
    return $db->single()->id;
  }

  public function isSuperAdmin() {
    if(isset($_SESSION['id'])){
      if ("SA" === $this->rank) {
        // $db = new database();
        // $db->query("SELECT rank FROM tbl_user WHERE tbl_user.id = :id");
        // $db->bind(':id',$_SESSION['id']);
        // if ($db->single()->rank === 'SA') {
        //   return true;
        // }
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function getUserActiveMetaFields($id){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT bg_user_meta.value,
      field.name AS field,
      field.id AS field_id
      FROM bg_user_meta
      LEFT JOIN bg_meta_fields AS field ON bg_user_meta.field = field.id
      WHERE `user` = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function updateUserMeta($field, $value){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_user_meta (`user`, field, `value`, `timestamp`)
      VALUES (?, ?, ?, NOW())
      ON DUPLICATE KEY UPDATE `value` = ?;");
    $db->bind(1, $this->id);
    $db->bind(2, $field);
    $db->bind(3, $value);
    $db->bind(4, $value);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Meta field updated!");
  }

  public function canUserManageOrg($org){
    if($this->isSuperAdmin()){
      return true;
    }
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT relation FROM tbl_org_members
      WHERE org = ? AND user = ?");
    $db->bind(1,$org);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if ('L' === $db->single()->relation) return true;
    return false;
  }

  public function changeUserOrgMemberStatus($org, $relation, $user=null){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("INSERT INTO tbl_org_members (org, `user`, relation,
      `timestamp`) VALUES(?,?,?,NOW())
      ON DUPLICATE KEY UPDATE relation = ?");
    $db->bind(1, $org);
    $db->bind(2, $user);
    $db->bind(3, $relation);
    $db->bind(4, $relation);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return TRUE;
  }

  public function applyToOrg($org){
    $user = new user();
    $this->changeUserOrgMemberStatus($org, 'R', $user->id);
    return returnSuccess("You have applied to this organization");
  }

  public function approveOrgMembership($user,$org){
    if(!$this->canUserManageOrg($org)){
      return returnError("You do not have permission to do this.");
    }
    $user = new user(FALSE, $user);
    $this->changeUserOrgMemberStatus($org, 'M', $user->id);
    return returnSuccess("$user->username is now a member of this organization.");
  }

  public function promoteToLeader($user,$org){
    if(!$this->canUserManageOrg($org)){
      return returnError("You do not have permission to do this.");
    }
    $user = new user(FALSE, $user);
    $this->changeUserOrgMemberStatus($org, 'L', $user->id);
    return returnSuccess("$user->username is now a leader of this organization.");
  }

  public function demoteOrgLeader($user,$org){
    if(!$this->canUserManageOrg($org)){
      return returnError("You do not have permission to do this.");
    }
    $user = new user(FALSE, $user);
    $this->changeUserOrgMemberStatus($org, 'M', $user->id);
    return returnSuccess("$user->username is no longer a leader of this organization.");
  }

  public function addMemberToOrg($user,$org){
    if(!$this->canUserManageOrg($org)){
      return returnError("You do not have permission to do this.");
    }
    $user = new user(FALSE, $user);
    $this->changeUserOrgMemberStatus($org, 'M', $user->id);
    return returnSuccess("$user->username is now a member of this organization.");
  }

  public function removeOrgMembership($user, $org){
    if(!$this->canUserManageOrg($org)){
      return returnError("You do not have permission to do this.");
    }
    $user = new user($user);
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("DELETE FROM tbl_org_members WHERE user = ? AND org = ?");
    $db->bind(1,$user->id);
    $db->bind(2,$org);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("$user->username has been removed from this organization");
  }

}