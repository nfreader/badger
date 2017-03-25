<header id="masthead" class="bg-black white center">
  <div class="w-90-m w-two-thirds-ns center">

    <nav class="pv3">
      <a class="link dim white b f6 dib mr3" href="<?php echo $app->app_URL;?>index.php" title="Home">App Name</a>
      <div class="fr">
      <?php if($user->online):?>
        Logged in as <?php echo $user->label;?>
      </div>
      <?php endif;?>
    </nav>
  </div>

</header>