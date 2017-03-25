<?php require_once('../config.php');
$app = new app();
$user = new user();
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/assets/bower/tachyons/css/tachyons.min.css"/>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/assets/bower/font-awesome/css/font-awesome.min.css"/>
</head>
<body class="ma0 ph0 sans-serif">
  <?php include ($app->app_ROOT.'/view/nav.php'); ?>
  <?php include ($app->app_ROOT.'/tests/testHeader.php'); ?>

  <div class="w-90-m w-two-thirds-ns center">
  <h1 class="f1 lh-title bb b-black bw3"><code>user</code> test suite</h1>
    <?php
      $username = 'testing_user'.time();
      $password = sha1($_SERVER['REMOTE_ADDR']);
      $password2 = sha1($_SERVER['REMOTE_ADDR']);
      $email = 'test_email@domain.tld'.time();
    ?>

    <?php if(DEBUG):?>
    <?php
      $result = $user->register($username, $password, $password2, $email);
      $expected = array('{"message":"Your account has been created and activated. Please log in.","level":1}');
     echo testResult($result,$expected,'Registration (debug mode)');
    ?>
    <?php else: ?>
    <?php
      $result = $user->register($username, $password, $password2, $email);
      $expected = array('{"message":"Your account has been created and requires activation. Please check your email for the activation link.","level":1}');
     echo testResult($result,$expected,'Registration');
    ?>
    <?php endif;?>

    <?php
      $result = $user->register($username, $password, $password2, $email);
      $expected = '{"message":"Email address or username already in use.","level":0}';
     echo testResult($result,$expected,'Duplicate email');
    ?>

    <?php
      $result = $user->register($username, $password, 'fish', $email.time());
      $expected = '{"message":"Passwords do not match!","level":0}';
     echo testResult($result,$expected,'Password mismatch');
    ?>

    <?php
      $result = $user->register($username, $password, $password2, '');
      $expected = '{"message":"You must specify an email address.","level":0}';
     echo testResult($result,$expected,'Empty email');
    ?>

    <?php
      $result = $user->register($username, '', '', $email.time());
      $expected = '{"message":"Password cannot be empty.","level":0}';
     echo testResult($result,$expected,'Empty password');
    ?>

    <?php
      $result = $user->login($username, $password);
      $expected = '{"message":"You are now logged in as '.$username.'.","level":1}';
     echo testResult($result,$expected,'Login');
    ?>

    <?php
      $result = $user->login('testuser', $password);
      $expected = '{"message":"Incorrect password","level":0}';
     echo testResult($result,$expected,'Login (wrong user)');
    ?>

    <?php
      $result = $user->login($username, $password.time());
      $expected = '{"message":"Incorrect password","level":0}';
     echo testResult($result,$expected,'Login (wrong password)');
    ?>

    <?php
      $result = $user->logout();
      $expected = '{"message":"You have logged out.","level":1}';
     echo testResult($result,$expected,'Logout');
    ?>

    <?php $db = new database();
    $db->query("DELETE FROM tbl_user WHERE username = ?");
    $db->bind(1,$username);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    ?>

  </div>

  <footer id="colophon" class="pa3 bt b--gray bg-white f6">
    <div class="w-90-m w-two-thirds-ns center cf">
    <?php if(DEBUG):?>
      <div class="w-third fl">
        <?php echo "<code>".PHP_Timer::resourceUsage()."</code>";?>
      </div>
      <div class="w-third fl">
        <strong class="db">$_POST</strong>
        <?php var_dump($_POST);?>
      </div>
      <div class="w-third fl">
        <strong class="db">$_GET</strong>
        <?php var_dump($_GET);?>
      </div>
    <?php endif;?>
    </div>
  </footer>

</body>
</html>