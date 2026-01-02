<?php
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: index.php?menu=2');
  exit;
}

$stmt = $conn->prepare("
  SELECT v.*
  FROM vijesti v
  WHERE v.id = ? AND v.odobreno = 1
  LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$vijest = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$vijest) {
  echo "<p>Vijest ne postoji ili nije odobrena.</p>";
  echo '<p><a href="index.php?menu=2">← Povratak na novosti</a></p>';
  return;
}

$slike = [];
$stmt = $conn->prepare("SELECT putanja FROM vijesti_slike WHERE vijest_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res) {
  $slike = $res->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

$datum = !empty($vijest['datum_unosa']) ? date('d.m.Y.', strtotime($vijest['datum_unosa'])) : '';
?>

<article class="news-single">
  <header>
    <h2><?= htmlspecialchars($vijest['naslov']) ?></h2>
    <p class="news-date">Datum objave: <?= htmlspecialchars($datum) ?></p>
  </header>

  <?php if (!empty($vijest['naslovna_slika'])): ?>
    <figure>
      <img src="<?= htmlspecialchars($vijest['naslovna_slika']) ?>" alt="Naslovna slika">
      <figcaption>Naslovna slika vijesti</figcaption>
    </figure>
  <?php endif; ?>

  <section class="news-text">
    <?php
      // tekst može biti običan, ali ovo ga lijepo podijeli na odlomke
      $parts = preg_split("/\r\n|\n|\r/", trim($vijest['tekst']));
      $printed = 0;
      foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') continue;
        echo '<p>' . nl2br(htmlspecialchars($p)) . '</p>';
        $printed++;
      }

      // ako nema dovoljno odlomaka, barem neće biti prazno
      if ($printed === 0) {
        echo '<p>' . nl2br(htmlspecialchars($vijest['tekst'])) . '</p>';
      }
    ?>
  </section>

  <?php if (!empty($slike)): ?>
    <h3>Galerija slika članka</h3>
    <div class="gallery-grid">
      <?php foreach ($slike as $i => $s): ?>
        <figure class="gallery-figure">
          <a href="<?= htmlspecialchars($s['putanja']) ?>" target="_blank" rel="noopener">
            <img src="<?= htmlspecialchars($s['putanja']) ?>" alt="Slika <?= (int)($i+1) ?>">
          </a>
          <figcaption>Slika <?= (int)($i+1) ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <p style="margin-top:16px;">
    <a href="index.php?menu=2">← Povratak na novosti</a>
  </p>
</article>
