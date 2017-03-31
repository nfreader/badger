<?php
if (!isset($_GET['org'])) echo alert('No organization found!',0);
if (!isset($_GET['member'])) echo alert('No member found!',0);
$org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
$org = new organization($org,TRUE);
// var_dump($org);
$member = filter_input(INPUT_GET, 'member', FILTER_SANITIZE_NUMBER_INT);
$member = array_filter(
    $org->members,
    function ($e) use (&$member) {
        return $e->id == $member;
    }
);
$member = $member[0];

?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
  <li class="di"><?php echo $org->link;?>/</li>
  <li class="di">Member/</li>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
  <?php echo $member->username;?>
  <small class="f4 gray"><?php echo $member->relationStatus;?>, since <?php echo $member->since;?>
    <?php if($user->canUserManageOrg($org->id)):?>
      <?php echo $member->relationLink;?>
    <?php endif;?>
  </small>
</h2>
