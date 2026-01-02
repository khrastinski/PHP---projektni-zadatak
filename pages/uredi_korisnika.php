<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

if (!is_logged_in()) {
    header('Location: index.php?menu=7');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?menu=8');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $grad = trim($_POST['grad'] ?? '');
    $ulica = trim($_POST['ulica'] ?? '');
    $datum_rodenja = $_POST['datum_rodenja'] ?? '';
    $drzava_id = (int)($_POST['drzava_id'] ?? 0);

    if ($ime === '' || $prezime === '' || $email === '' || $drzava_id <= 0) {
        $error = 'Popuni obavezna polja (ime, prezime, email, država).';
    } else {
        $stmt = $conn->prepare("
            UPDATE korisnici
            SET ime=?, prezime=?, email=?, drzava_id=?, grad=?, ulica=?, datum_rodenja=?
            WHERE id=?
        ");
        $stmt->bind_param("sssisssi", $ime, $prezime, $email, $drzava_id, $grad, $ulica, $datum_rodenja, $id);
        $stmt->execute();
        $stmt->close();

        header('Location: index.php?menu=8');
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM korisnici WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$korisnik = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$korisnik) {
    header('Location: index.php?menu=8');
    exit;
}

$drzave = [];
$res = $conn->query("SELECT id, naziv FROM drzave ORDER BY naziv");
if ($res) $drzave = $res->fetch_all(MYSQLI_ASSOC);
?>

<h2>Uredi korisnika</h2>

<?php if ($error !== ''): ?>
  <p style="color:#ffb3b3; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php?menu=10&id=<?= (int)$korisnik['id'] ?>">
  <label>Ime*</label>
  <input type="text" name="ime" value="<?= htmlspecialchars($korisnik['ime']) ?>" required>

  <label>Prezime*</label>
  <input type="text" name="prezime" value="<?= htmlspecialchars($korisnik['prezime']) ?>" required>

  <label>Email*</label>
  <input type="email" name="email" value="<?= htmlspecialchars($korisnik['email']) ?>" required>

  <label>Država*</label>
  <select name="drzava_id" required>
    <option value="">-- odaberi --</option>
    <?php foreach ($drzave as $d): ?>
      <option value="<?= (int)$d['id'] ?>" <?= ((int)$korisnik['drzava_id'] === (int)$d['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($d['naziv']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label>Grad</label>
  <input type="text" name="grad" value="<?= htmlspecialchars($korisnik['grad']) ?>">

  <label>Ulica</label>
  <input type="text" name="ulica" value="<?= htmlspecialchars($korisnik['ulica']) ?>">

  <label>Datum rođenja</label>
  <input type="date" name="datum_rodenja" value="<?= htmlspecialchars($korisnik['datum_rodenja']) ?>">

  <button type="submit">Spremi</button>
  <a href="index.php?menu=8">Nazad</a>
</form>
