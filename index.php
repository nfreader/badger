<?php require_once('config.php');
$app = new app();
// if($user->online) $app->include = 'home';
if (isset($_GET['action'])) {
  $app->action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
  require_once('action.php');
}

$user = new user(TRUE);

?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/assets/bower/tachyons/css/tachyons.min.css"/>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/assets/bower/font-awesome/css/font-awesome.min.css"/>
  <link rel="stylesheet" href="<?php echo $app->app_URL;?>/assets/css/style.css"/>
</head>
<body class="ma0 ph0 sans-serif">
  <?php include ('view/nav.php'); ?>

  <div class="center pa3 w-90-m w-two-thirds-ns ph0-ns">
    <?php include('view/msg.php'); ?>
    <h1 class="f1 lh-title bb b-black bw3"><i class='fa fa-id-card-o'></i> BadgeR</h1>

    <?php include("view/$app->include.php"); ?>
  </div>

  <footer id="colophon" class="pa3 bt b--light-gray bg-white f6">
    <div class="center pa3 w-90-m w-two-thirds-ns ph0-ns">
    <div class="w-50 fl pl2">
    <?php if ($user->online):?>
      <a class="ph3 pv1 b input-reset ba b--black bg-transparent hover-bg-black hover-white pointer link black" href="?action=logout">Logout</a>
    <?php endif;?>
    </div>
    <?php if(DEBUG):?>
      <div class="w-50 fl pl2">
        <?php echo "<code>".PHP_Timer::resourceUsage()."</code>";?>
      </div>
      </div>
      <div class="center pa3 w-90-m w-two-thirds-ns ph0-ns">
      <div class="w-50 fl pr2">
        <strong class="db">$_POST</strong>
        <?php var_dump($_POST);?>
        <strong class="db">$_GET</strong>
        <?php var_dump($_GET);?>
      </div>
      <div class="w-50 fl pr2">
      <strong class="db">$_SESSION</strong>
      <?php var_dump($_SESSION);?>
      </div>
    <?php endif;?>
    </div>
  </footer>

</body>
</html>