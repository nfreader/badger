<h2 class="f2 lh-title bb b-black bw1 mt0">
User meta fields
</h2>

<?php $fields = $app->getUserMetaFields();?>
<?php if ($fields):?>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Used by members</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($fields as $f):?>
      <tr>
        <td><?php echo $f->name;?></td>
        <td><?php echo $f->members;?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
<?php else:?>
  <?php echo alert('No fields yet. Create one'); ?>
<?php endif; ?>
<div class="cf mt4 ba b-black bg-washed-red pa3">
  <h3>Add new user meta fields</h3>
<form method="POST" action="index.php?action=newUserMetaField">
    <div class="cf mb4">
      <label class="db b w-100 mb1" for="name">Name</label>
      <input class="db w-100" name="name" type="text"/>
    </div>
    <div>
      <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-red pointer" type="submit" value="Create Field">
    </div>
  </form>
</div>