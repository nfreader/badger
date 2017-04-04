<?php
$ability = null;
if (isset($_GET['ability'])){
  $ability = filter_input(INPUT_GET, 'ability', FILTER_SANITIZE_NUMBER_INT);
  $ability = new ability($ability,TRUE);
  $org = new organization($ability->org);
  // var_dump($ability);
} else {
  if (!isset($_GET['org'])) echo alert('No organization found!',0);
  $org = filter_input(INPUT_GET, 'org', FILTER_SANITIZE_NUMBER_INT);
  $org = new organization($org,TRUE);
}

?>

<ul class="ma0 pa0 courier bb b--gray gray">
  <li class="di"><a href="index.php" class="link gray">Organization</a>/</li>
  <li class="di"><?php echo $org->link;?>/</li>
  <?php if ($ability):?>
    <li class="di"><a href="?action=manageAbilities&org=<?php echo $org->id;?>">Abilities</a>/</li>
  <?php endif;?>
</ul>

<h2 class="f2 lh-title bb b-black bw1 mt0 pl3">
<?php if ($ability):?>
  <?php echo $ability->name;?>
<?php else:?>
  Abilities
<?php endif;?>
</h2>

<?php if ($ability):?>
  <div class="tc">
    <?php echo $ability->html;?><br>
    <p class='f3'><?php echo $ability->desc;?></p>
  </div>
<?php elseif (!$org->abilities):?>
  <?php echo alert("No abilities.");?>
<?php else:?>
  <?php foreach ($org->abilities as $a):?>
    <?php echo $a->html;?>
  <?php endforeach;?>
<?php endif;?>

<div class="bb grey mv4"></div>

<div class="cf">
  <div class="cf mt4 ba b-black bg-washed-yellow pa3 w-60 fl">
    <h3>Add new organization ability</h3>
    <form method="POST" action="index.php?action=newAbility&org=<?php echo $org->id;?>" id="newAbility">
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="name">Name</label>
        <input class="db w-100" name="name" id="text" type="text"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="name">Icon</label>
        <input class="db w-100" name="icon" id="icon" type="text"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="name">Color</label>
        <div id="color">
        </div>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="name">Description</label>
        <textarea name="desc" rows="5" class="w-100"></textarea>
      </div>
      <div>
        <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-yellow pointer" type="submit" value="Create Ability">
      </div>
    </form>
  </div>
  <div class="cf mt4 ba b-black pa3 w-30 fr">
    <h3>Preview <small><a href='#' id='render'>Render</a></small></h3>
    <div id="preview">
      <div class="dib ba pa2 b br2 f5" data-color='green' data-icon='rocket'>
        <i class="fa fa-fw" title="Certified Pilot"></i> <span id="text">Certified Pilot</span>
      </div>
    </div>
  </div>
</div>
<style>

.skintone-sel {
  padding: 10px;
}
input[name=color] {
  display: none;
}

input[name=color] + label {
  border: 3px solid grey;
  border-radius: 4px;
  padding: 10px;
  margin: 0 2px 0 0;
  display: inline-block;
}

input[name=color]:checked + label {
  border-color: black;
}

input[name=color]:disabled + label {
  opacity: .75;
}
</style>
<script src="<?php echo $app->app_URL;?>/assets/bower/jquery/dist/jquery.min.js"></script>
<script>
$('#preview div').addClass($(this).attr('data-color'));
var icon = 'fa-'+$('#preview div').attr('data-icon');
$('#preview div i').addClass(icon);
var colors = {
"dark-red":"#e7040f",
"red":"#ff4136",
"light-red":"#ff725c",
"orange":"#ff6300",
"purple":"#5e2ca5",
"light-purple":"#a463f2",
"dark-pink":"#d5008f",
"hot-pink":"#ff41b4",
"pink":"#ff80cc",
"dark-green":"#137752",
"green":"#19a974",
"navy":"#001b44",
"dark-blue":"#00449e",
"blue":"#357edd"};
$.each(colors,function(i,v){
  var option = "<input type='radio' name='color' value='"+i+"' class='color-sel' id='color-"+i+"'><label for='color-"+i+"' style='background: "+v+"'></label>";
  $('#color').append(option);
});
$('.color-sel').click(function(e){
  $('#preview div').removeClass($('#preview div').attr('data-color'));
  $('#preview div').attr('data-color',$(this).val());
  $('#preview div').addClass($('#preview div').attr('data-color'));
});

$('#text').on('change', function(e){
  $('#preview div #text').text($(this).val());
});

$('#icon').on('change', function(e){
  var oldicon = 'fa-'+$('#preview div').attr('data-icon');
  var newicon = 'fa-'+$(this).val();
  $('#preview div i').removeClass(oldicon);
  $('#preview div').attr('data-icon',newicon);
  $('#preview div i').addClass(newicon);
  console.log(oldicon);
  console.log(newicon);
});
$('#render').click(function(e){
  $('newAbility').submit(function(e){
    e.preventDefault();
  })
})
</script>