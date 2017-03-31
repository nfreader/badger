<?php
if (!isset($_GET['org'])) echo alert('No organization found!',0);
$org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
$org = new organization($org,TRUE);
$users = $org->getUsersNotInOrg($org->id);
?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
  <li class="di"><?php echo $org->link;?>/</li>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
  Manually Add Members
</h2>

<h3 class="f4 lh-title bb b-black">
  Organization Roster <small><?php echo $org->membercount;?></small>
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
        <td><?php echo $m->relationIcon;?> <?php echo $m->username;?></td>
        <td><?php echo $m->relationStatus;?>
        <?php if($user->canUserManageOrg($org->id)){
          echo $m->relationLink;
          }?></td>
        <td><?php echo $m->relationAge;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>

<h3 class="f4 lh-title bb b-black">
 Users not in <?php echo $org->name;?>
</h3>

<?php if (!$users):?>
  <?php echo alert("No users.");?>
<?php else:?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Since</th>
        <th>Member Status</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u):?>
      <tr>
        <td><?php echo $u->username;?></td>
        <td><?php echo $u->since;?>
        <td><?php echo btn("Add user","addUserToOrg&org=$org->id&user=$u->id",TRUE);?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>