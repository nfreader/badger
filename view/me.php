<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Members</a>/</li>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mv0 pl3">
  <?php echo $user->username;?>
  <small class="f4 gray">Since <?php echo $user->since;?></small>
</h2>
<div class="pa2 tc b mb4 <?php echo "bg-$user->backColor $user->foreColor";?>">
  <?php echo $user->fullRank;?>
</div>

<h2 class="f4 lh-title bb b-black">
  My Organizations
</h2>

<?php if (!$user->orgs):?>
  <?php echo alert("You are not a member of any organizations.");?>
<?php else:?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Members</th>
        <th>Since</th>
        <th>My Status</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($user->orgs as $o):?>
      <tr class="bg-<?php echo $o->class;?>">
        <td><?php echo $o->link;?></td>
        <td><?php echo $o->membercount;?></td>
        <td><?php echo $o->created;?></td>
        <td><?php echo $o->userStatus;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php endif;?>

<h2 class="f4 lh-title bb b-black">
  My Abilities
</h2>

<?php if (!$user->abilities):?>
  <?php echo alert("You do not have any abilities.");?>
<?php else:?>

<?php endif;?>

<h2 class="f4 lh-title bb b-black">
  User Meta Fields
  <?php if ($user->isSuperAdmin()):?>
    <small><a href="?action=userMetaFields">Edit Fields</a></small>
  <?php endif;?>
</h2>

<?php $fields = $app->getUserMetaFields(); ?>

<?php if ($fields):?>
  <table class="table bg-washed-green">
    <thead>
      <tr>
        <th>Name</th>
        <th>My value</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($fields as $f):?>
      <?php $f->value = '';?>
      <?php foreach($user->metadata as $m):?>
        <?php $value = '';
        if ($m->field_id === $f->id) {
          $f->value = $m->value;
        }?>
      <?php endforeach;?>
      <tr>
        <td><?php echo $f->name;?></td>
        <td>
          <form action="?action=updateUserMeta&field=<?php echo $f->id;?>"
           method="POST">
            <input class="pv1 dib w-70" name="value" value="<?php echo $f->value;?>" />
            <input class="w-20 dib ph3 pv1 b input-reset ba b--black bg-transparent hover-bg-black hover-white pointer" type="submit" value="Submit">
          </form>
        </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php else:?>
  <?php echo alert("No meta fields defined.");?>
<?php endif; ?>