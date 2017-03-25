<?php if(!$user->online) {
  include('guest.php'); return;
} ?>

<?php if($user->isSuperAdmin()):?>

<?php $orgs = new organization();
  $orgs = $orgs->getOrganizations();
  if ($orgs): ?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Members</th>
        <th>Since</th>
        <th>Public</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($orgs as $o):?>
      <tr>
        <td><?php echo $o->link;?></td>
        <td>0</td>
        <td><?php echo $o->created;?></td>
        <td class="<?php echo ($o->public)?'bg-washed-green':'bg-washed-red';?>">
        <?php echo $o->publicStatus;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>

  <?php else: ?>
  <?php echo alert('No organizations yet. Create one'); ?>
  <?php endif; ?>
  <div class="cf mt4 ba b-black bg-washed-red pa3">
    <h3>Add new organization</h3>
    <form method="POST" action="index.php?action=newOrg">
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="organization">Orginzation Name</label>
        <input class="db w-100" name="organization" type="text"/>
      </div>
      <div>
        <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-red pointer" type="submit" value="Create Organization">
      </div>
    </form>
  </div>

<?php else:?>

<?php endif;?>
