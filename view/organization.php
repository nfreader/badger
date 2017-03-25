<?php
if (!isset($_GET['org'])) echo alert('No organization found!',0);
$org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
$org = new organization($org);
?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
  <?php echo $org->name;?>
  <small class="f4 gray">Since <?php echo $org->since;?>, <?php echo $org->publicStatus;?> <a href="?action=flipOrgPublic&org=<?php echo $org->id;?>">Change</a></small>
</h2>