<nav>
  <ul>
    <li><a href="index.php?menu=1">Početna stranica</a></li>
    <li><a href="index.php?menu=2">Novosti</a></li>
    <li><a href="index.php?menu=3">Kontakt</a></li>
    <li><a href="index.php?menu=4">O nama</a></li>
    <li><a href="index.php?menu=5">Galerija</a></li>

    <?php if (!is_logged_in()): ?>
      <li><a href="index.php?menu=6">Registracija</a></li>
      <li><a href="index.php?menu=7">Prijava</a></li>
    <?php else: ?>
      <?php if (can_access_cms()): ?>
        <li><a href="index.php?menu=11">Administracija</a></li>
        <?php if (role() === 'administrator'): ?>
          <li><a href="index.php?menu=12">Uredi korisnike</a></li>
        <?php endif; ?>
        <li><a href="index.php?menu=13">Dodaj vijesti</a></li>
      <?php endif; ?>
      <li><a href="index.php?menu=9">Odjava</a></li>
    <?php endif; ?>
  </ul>
</nav>
