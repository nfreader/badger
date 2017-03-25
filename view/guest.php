<p class="pa0 ma0">This is <code>BadgeR</code>, a tool to manage organization members, skills and abilities, team memberships, and printed credentials.</p>

<div class="cf mt4 ba b-black bg-washed-green pa3">
  <div class="fl w-50 pr2">
    <h2>Register</h2>
    <form method="POST" action="index.php?action=register">
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="username">Username</label>
        <input class="db w-100" name="username" type="text"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="password">Password</label>
        <input class="db w-100" name="password" type="password"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="password-again">Password (again)</label>
        <input class="db w-100" name="password2" type="password"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="username">Email</label>
        <input class="db w-100" name="email" type="email"/>
      </div>
      <div>
        <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-green pointer" type="submit" value="Register">
      </div>
    </form>
  </div>

  <div class="fr w-50 pl2">
    <h2>Log in</h2>
    <form method="POST" action="index.php?action=login">
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="username">Username</label>
        <input class="db w-100" name="username" type="text"/>
      </div>
      <div class="cf mb4">
        <label class="db b w-100 mb1" for="password">Password</label>
        <input class="db w-100" name="password" type="password"/>
      </div>
      <div>
        <input class="ph3 pv1 b input-reset ba b--black bg-transparent db w-100 hover-bg-black hover-washed-green pointer" type="submit" value="Sign in">
      </div>
    </form>
  </div>
</div>