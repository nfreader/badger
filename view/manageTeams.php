<?php
$team = false;
if (isset($_GET['team'])){
  $team = filter_input(INPUT_GET, 'team', FILTER_SANITIZE_NUMBER_INT);
  $org = new organization();
  $team = $org->getTeam($team);
  $org = new organization($team->id,TRUE);
  $diff = false;
  $diff = array_udiff($org->abilities, $team->requirements,
    function ($obj_a, $obj_b) {
      return $obj_a->id - $obj_b->id;
    }
  );
} else {
if (!isset($_GET['org'])) echo alert('No organization found!',0);
  $org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
  $org = new organization($org,FALSE);
}
?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
  <li class="di"><?php echo $org->link;?>/</li>
  <?php if ($team):?>
    <li class="di"><a href="?action=manageTeams&org=<?php echo $org->id;?>">Teams</a>/</li>
  <?php endif;?>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
<?php if ($team):?>
  <?php echo $team->name;?>
<?php else:?>
  Teams
<?php endif;?>
</h2>

<?php if($team):?>
  <h3 class="f4 lh-title bb b-black">
    Manage Team Abilities<small>
      <?php if($user->canUserManageOrg($org->id)):?>
        <a href="?action=manageAbilities&org=<?php echo $org->id;?>">Manage Abilities</a>
      <?php endif;?>
    </small>
  </h3>
  <div class="cf">
    <div class="w-50 ph2 fl">
      <h3 class="f4 lh-title bb b-black">Available Abilities</h3>
      <?php foreach ($diff as $a):?>
        <div class="dib">
          <?php echo $a->html;?>
          <?php if($user->canUserManageOrg($org->id)):?>
            <br><small><a href="?action=addTeamRequirement&team=<?php echo $team->id;?>&ability=<?php echo $a->id;?>">Add</a></small>
          <?php endif;?>
        </div>
      <?php endforeach;?>
    </div>
    <div class="w-50 ph2 fl">
      <h3 class="f4 lh-title bb b-black">Required Abilities</h3>
      <?php foreach ($team->requirements as $a):?>
        <div class="dib">
          <?php echo $a->html;?>
          <?php if($user->canUserManageOrg($org->id)):?>
            <br><small><a href="?action=removeTeamRequirement&team=<?php echo $team->id;?>&ability=<?php echo $a->id;?>">Remove</a></small>
          <?php endif;?>
        </div>
      <?php endforeach;?>
    </div>
  </div>
<?php endif;?>

<?php if(!$team && $user->canUserManageOrg($org->id)):?>
<div class="cf mt4 ba b-black bg-washed-yellow pa3">
  <h3>Add new team to <?php echo $org->name;?></h3>
  <form method="POST" action="index.php?action=newTeam&org=<?php echo $org->id;?>">
    <div class="cf mb4">
      <label class="db b w-100 mb1" for="name">Name</label>
      <input class="db w-100" name="name" id="text" type="text"/>
    </div>
    <div>
      <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-yellow pointer" type="submit" value="Create Team">
    </div>
  </form>
</div>
<?php endif;?>