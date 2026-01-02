<?php
require __DIR__ . '/../includes/db.php';

$errors = [];
$success = '';

$drzave = [];
$r = $conn->query("SELECT id, naziv FROM drzave ORDER BY naziv");
while ($row = $r->fetch_assoc()) $drzave[] = $row;

function make_username(mysqli $conn, string $ime, string $prezime): string {
  $base = mb_strtolower(mb_substr($ime, 0, 1) . preg_replace('/\s+/', '', $prezime));
  $base = preg_replace('/[^a-z0-9čćđšž]/u', '', $base);

  $username = $base;
  $i = 1;

  $stmt = $conn->prepare("SELECT id FROM korisnici WHERE korisnicko_ime = ? LIMIT 1");
  while (true) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) break;
    $username = $base . $i;
    $i++;
  }
  $stmt->close();
  return $username;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ime = trim($_POST['ime'] ?? '');
  $prezime = trim($_POST['prezime'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $drzava_id = (int)($_POST['drzava_id'] ?? 0);
  $grad = trim($_POST['grad'] ?? '');
  $ulica = trim($_POST['ulica'] ?? '');
  $datum_rodenja = trim($_POST['datum_rodenja'] ?? '');
  $lozinka = (string)($_POST['lozinka'] ?? '');

  if ($ime === '' || $prezime === '' || $email === '' || $drzava_id <= 0 || $grad === '' || $ulica === '' || $datum_rodenja === '' || $lozinka === '') {
    $errors[] = 'Popuni sva obavezna polja.';
  }

  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email nije ispravan.';
  }

  if ($lozinka !== '' && strlen($lozinka) < 6) {
    $errors[] = 'Lozinka mora imati barem 6 znakova.';
  }

  if (!$errors) {
    $stmt = $conn->prepare("SELECT id FROM korisnici WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $errors[] = 'Taj email je već registriran.';
      $stmt->close();
    } else {
      $stmt->close();

      $korisnicko_ime = make_username($conn, $ime, $prezime);
      $hash = password_hash($lozinka, PASSWORD_DEFAULT);

      $rola = 'user';
      $user_cms = 0;
      $aktivan = 0; 

      $ins = $conn->prepare("
        INSERT INTO korisnici
        (ime, prezime, email, korisnicko_ime, drzava_id, grad, ulica, datum_rodenja, lozinka_hash, user_rola, user_cms, aktivan)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
      ");
      $ins->bind_param("ssssisssssis", $ime, $prezime, $email, $korisnicko_ime, $drzava_id, $grad, $ulica, $datum_rodenja, $hash, $rola, $user_cms, $aktivan);
      $ins->execute();
      $newId = (int)$ins->insert_id;
      $ins->close();

      $success = "Registracija uspješna. Vaše korisničko ime je: $korisnicko_ime";

      
      $_SESSION['user_id'] = $newId;
      $_SESSION['user_ime'] = $ime;
      $_SESSION['user_prezime'] = $prezime;
      $_SESSION['user_rola'] = $rola;
      $_SESSION['user_cms'] = $user_cms;

      header('Location: index.php?menu=1');
      exit;
    }
  }
}
?>

<section id="registracija">
  <h2>Registracija</h2>

  <?php if ($success): ?>
    <p style="background: rgba(0,128,0,0.25); padding: 10px; border-radius: 8px;"><?php echo htmlspecialchars($success); ?></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div style="background: rgba(255,0,0,0.20); padding: 10px; border-radius: 8px; margin-bottom: 14px;">
      <ul style="margin: 0; padding-left: 18px;">
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="form-wide">
    <label for="ime">Ime*</label>
    <input type="text" id="ime" name="ime" required value="<?php echo htmlspecialchars($_POST['ime'] ?? ''); ?>">

    <label for="prezime">Prezime*</label>
    <input type="text" id="prezime" name="prezime" required value="<?php echo htmlspecialchars($_POST['prezime'] ?? ''); ?>">

    <label for="email">E-mail adresa*</label>
    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

    <label for="drzava_id">Država*</label>
    <select id="drzava_id" name="drzava_id" required>
      <option value="">Odaberi državu</option>
      <?php
        $selected = (int)($_POST['drzava_id'] ?? 0);
        foreach ($drzave as $d) {
          $sel = ($selected === (int)$d['id']) ? 'selected' : '';
          echo '<option value="' . (int)$d['id'] . '" ' . $sel . '>' . htmlspecialchars($d['naziv']) . '</option>';
        }
      ?>
    </select>

    <label for="grad">Grad*</label>
    <input type="text" id="grad" name="grad" required value="<?php echo htmlspecialchars($_POST['grad'] ?? ''); ?>">

    <label for="ulica">Ulica*</label>
    <input type="text" id="ulica" name="ulica" required value="<?php echo htmlspecialchars($_POST['ulica'] ?? ''); ?>">

    <label for="datum_rodenja">Datum rođenja*</label>
    <input type="date" id="datum_rodenja" name="datum_rodenja" required value="<?php echo htmlspecialchars($_POST['datum_rodenja'] ?? ''); ?>">

    <label for="lozinka">Lozinka*</label>
    <input type="password" id="lozinka" name="lozinka" required>

    <button type="submit">Registriraj se</button>
  </form>
</section>
