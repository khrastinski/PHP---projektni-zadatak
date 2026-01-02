<?php
require __DIR__ . '/../includes/auth.php';
require_cms();

$rola = role(); 
?>

<div class="cms-box">
  <h2>Administracija</h2>

  <p>
    Prijavljeni ste kao:
    <strong><?= htmlspecialchars(ucfirst($rola)) ?></strong>
  </p>

  <ul>
    <?php if ($rola === 'administrator'): ?>
      <li><a href="index.php?menu=12">Upravljanje korisnicima</a></li>
      <li><a href="index.php?menu=13">Sve vijesti</a></li>
      <li><a href="index.php?menu=13&filter=neodobrene">Odobravanje vijesti</a></li>

    <?php elseif ($rola === 'editor'): ?>
      <li><a href="index.php?menu=13">Vijesti</a></li>

    <?php else: ?>
      <li><a href="index.php?menu=14">Dodaj vijest</a></li>
      <li><em>Vijesti nisu javne dok ih administrator ne odobri.</em></li>
    <?php endif; ?>
  </ul>
</div>
