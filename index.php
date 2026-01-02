<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';


$menu = isset($_GET['menu']) ? (int)$_GET['menu'] : 1;

$routes = [
  1 => 'pocetna',
  2 => 'novosti',
  3 => 'kontakt',
  4 => 'o-nama',
  5 => 'galerija',
  6 => 'registracija',
  7 => 'login',
  8 => 'admin',
  9 => 'logout',
  10 => 'uredi_korisnika',
  11 => 'cms',
  12 => 'cms_korisnici',
  13 => 'cms_vijesti',
  14 => 'cms_vijesti_form',
  15 => 'vijest'
];

$page = $routes[$menu] ?? 'pocetna';


if (in_array($menu, [11, 12, 13, 14], true)) {
    require_cms();
}

if (in_array($menu, [8, 10, 12], true)) {   
    require_role(['administrator']);
}



require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/nav.php';
if ($menu === 8 && !is_logged_in()) {
  header('Location: index.php?menu=7');
  exit;
}
echo '<main>';
require __DIR__ . '/pages/' . $page . '.php';
echo '</main>';
require __DIR__ . '/includes/footer.php';
