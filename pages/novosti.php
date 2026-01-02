<?php
global $conn;

$static = [
  [
    'naslov' => '"LUČ ZAGORJA": Prošle godine udomljeno je 278 pasa...',
    'tekst'  => 'Sklonište za životinje "Luč Zagorja" iz Selnice izvještava...',
    'naslovna_slika' => '/Zabocki-lavovi/assets/img/tb1.jpg',
    'datum' => '14. ožujka 2022.',
    'url'   => 'https://www.zagorje-international.hr/2022/03/14/luc-zagorja-prosle-godine-udomljeno-je-278-pasa-a-ove-do-kraja-veljace-njih-45-pet-ih-je-otislo-za-veliku-britaniju/',
    'external' => true,
  ],
  [
    'naslov' => 'Zavodu za prostorno uređenje dodjeljeno 336.230 eura...',
    'tekst'  => 'Krapinsko-zagorska županija dodijelila je 336.230 eura...',
    'naslovna_slika' => '/Zabocki-lavovi/assets/img/tb2.jpg',
    'datum' => '22. siječnja 2023.',
    'url'   => 'https://www.zagorje-international.hr/2023/01/22/zavodu-za-prostorno-uredenje-dodjeljeno-336230-eura-za-financiranje-aktivnosti/',
    'external' => true,
  ],
  [
    'naslov' => '[VIDEO] Uhićen nasred ceste u okolici Zaboka',
    'tekst'  => 'Policija je u okolici Zaboka uhitila osobu...',
    'naslovna_slika' => '/Zabocki-lavovi/assets/img/tb3.jpg',
    'datum' => '2. rujna 2022.',
    'url'   => 'https://www.zagorje-international.hr/2022/09/02/video-uhicen-nasred-ceste-u-okolici-zaboka/',
    'external' => true,
  ],
  [
    'naslov' => 'Zabilježeno čak šest prometnih nesreća i nekoliko krađa',
    'tekst'  => 'Policijska uprava krapinsko-zagorska izvještava...',
    'naslovna_slika' => '/Zabocki-lavovi/assets/img/tb4.jpg',
    'datum' => '9. kolovoza 2022.',
    'url'   => 'https://www.zagorje-international.hr/2022/08/09/zabiljezeno-cak-sest-prometnih-nesreca-i-nekoliko-kraza/',
    'external' => true,
  ],
  [
    'naslov' => '"Čovjek je naslonio brane na drvo..."',
    'tekst'  => 'U Pregradi su mještani naišli na stare brane...',
    'naslovna_slika' => '/Zabocki-lavovi/assets/img/tb5.jpg',
    'datum' => '24. kolovoza 2022.',
    'url'   => 'https://www.zagorje-international.hr/2022/08/24/covjek-je-naslonio-brane-na-drvo-no-uslijed-vremenskih-uvjeta-su-pale-i-siljci-su-se-okrenuli/',
    'external' => true,
  ],
];

$res = $conn->query("
  SELECT id, naslov, tekst, naslovna_slika, datum_unosa
  FROM vijesti
  WHERE odobreno = 1 AND arhiva = 0
  ORDER BY datum_unosa DESC
");

$db = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$dbMapped = array_map(function($v){
  return [
    'id' => (int)$v['id'],
    'naslov' => $v['naslov'],
    'tekst' => $v['tekst'],
    'naslovna_slika' => $v['naslovna_slika'],
    'datum_unosa' => $v['datum_unosa'],
    'external' => false,
  ];
}, $db);

$all = array_merge($static, $dbMapped);
?>

<section id="novosti">
  <h2>Novosti</h2>

  <?php foreach ($all as $v): ?>
    <?php
      $isExternal = !empty($v['external']);
      $link = $isExternal
        ? ($v['url'] ?? '#')
        : ('index.php?menu=15&id=' . (int)$v['id']);

      $linkAttrs = $isExternal ? 'target="_blank" rel="noopener"' : '';
      $altText = !empty($v['naslov']) ? $v['naslov'] : 'Naslovna slika';
    ?>

    <div class="news-article">

      <?php if (!empty($v['naslovna_slika'])): ?>
        <a href="<?= htmlspecialchars($link) ?>" <?= $linkAttrs ?> class="news-thumb-link">
          <img
            src="<?= htmlspecialchars($v['naslovna_slika']) ?>"
            alt="<?= htmlspecialchars($altText) ?>"
            class="news-thumbnail"
            loading="lazy"
            decoding="async"
          >
        </a>
      <?php endif; ?>

      <h3>
        <a href="<?= htmlspecialchars($link) ?>" <?= $linkAttrs ?>>
          <?= htmlspecialchars($v['naslov']) ?>
        </a>
      </h3>

      <p class="news-excerpt">
        <?php
          $clean = trim(strip_tags($v['tekst'] ?? ''));
          $excerpt = mb_substr($clean, 0, 220);
          echo htmlspecialchars($excerpt) . (mb_strlen($clean) > 220 ? '...' : '');
        ?>
      </p>

      <p class="news-date">
        Objavljeno:
        <?php if ($isExternal): ?>
          <?= htmlspecialchars($v['datum'] ?? '') ?>
        <?php else: ?>
          <?= htmlspecialchars(date('d.m.Y.', strtotime($v['datum_unosa']))) ?>
        <?php endif; ?>
      </p>

      <a href="<?= htmlspecialchars($link) ?>" class="read-more" <?= $linkAttrs ?>>
        Više o članku &raquo;
      </a>
    </div>
  <?php endforeach; ?>
</section>
