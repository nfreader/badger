<?php
if (!isset($_GET['org'])) echo alert('No organization found!',0);
$org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
$org = new organization($org,TRUE);
?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
  <?php echo $org->name;?>
  <small class="f4 gray">Since <?php echo $org->since;?>, <?php echo $org->publicStatus;?> <?php if($user->canUserManageOrg($org->id)):?><a href="?action=flipOrgPublic&org=<?php echo $org->id;?>">Change</a><?php endif;?></small>
</h2>

<h3 class="f4 lh-title bb b-black">
  Organization Abilities <small>
    <?php if($user->canUserManageOrg($org->id)):?>
      <a href="?action=manageAbilities&org=<?php echo $org->id;?>">Manage</a>
    <?php endif;?>
  </small>
</h3>
<?php if (!$org->abilities):?>
  <?php echo alert("No abilities.");?>
<?php else:?>
  <?php foreach ($org->abilities as $a):?>
    <?php echo $a->html;?>
  <?php endforeach;?>
  <a class="dib ba pa2 b br2 f5 black link hover-bg-black pointer hover-white" href="?action=manageAbilities&org=<?php echo $org->id;?>"><i class="fa fa-fw fa-plus" title="Add Ability"></i> Add</a>
<?php endif;?>

<h3 class="f4 lh-title bb b-black">
  Organization Teams <small>
  <?php if($user->canUserManageOrg($org->id)):?>
    <a href="?action=manageTeams&org=<?php echo $org->id;?>">Manage</a>
  <?php endif;?>
  </small>
</h3>

<?php if (!$org->teams):?>
  <?php echo alert("No teams.");?>
<?php else:?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Member Count</th>
        <th>Required Abilities</th>
        <th>Since</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($org->teams as $t):?>
      <tr>
        <td><?php echo $t->link;?></td>
        <td><?php echo $t->membercount;?></td>
        <td><?php
        foreach ($t->requirements as $r){
          echo $r->html." ";
        }
        ?></td>
        <td><?php echo $t->since;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>

<h3 class="f4 lh-title bb b-black">
  Organization Roster <small><?php echo $org->membercount;?>
  <?php if($user->canUserManageOrg($org->id)):?>
    <a href="?action=addMember&org=<?php echo $org->id;?>">Add more</a>
  <?php endif;?>
  </small>
</h3>

<?php if (!$org->members):?>
  <?php echo alert("No members.");?>
<?php else:?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Member Status</th>
        <th>Since</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($org->members as $m):?>
      <tr class="bg-<?php echo $m->relationClass;?>">
        <td><?php echo $m->relationIcon;?> <?php echo $m->username;?>
          <?php echo $m->orgLink;?>
        </td>
        <td>
          <?php echo $m->relationStatus;?>
        </td>
        <td><?php echo $m->relationAge;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>