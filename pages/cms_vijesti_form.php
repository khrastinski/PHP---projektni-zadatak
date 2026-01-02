<?php
require_cms();
require_once __DIR__ . '/../includes/db.php';

$role = role();
$me = (int)($_SESSION['user_id'] ?? 0);

$uploadDirCover = __DIR__ . '/../assets/uploads/news';
$uploadDirGallery = __DIR__ . '/../assets/uploads/news/gallery';

if (!is_dir($uploadDirCover)) mkdir($uploadDirCover, 0777, true);
if (!is_dir($uploadDirGallery)) mkdir($uploadDirGallery, 0777, true);

$id = (int)($_GET['id'] ?? 0);
$editing = $id > 0;

$vijest = null;
if ($editing) {
    $stmt = $conn->prepare("SELECT * FROM vijesti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $vijest = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$vijest) {
        header('Location: index.php?menu=13');
        exit;
    }

    if ($role === 'user' && (int)$vijest['korisnik_id'] !== $me) {
        header('Location: index.php?menu=13');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = trim($_POST['naslov'] ?? '');
    $tekst = trim($_POST['tekst'] ?? '');

    if ($naslov === '' || $tekst === '') {
        $error = 'Naslov i tekst su obavezni.';
    } else {
        $naslovnaPath = $vijest['naslovna_slika'] ?? null;

        if (!empty($_FILES['naslovna']['name']) && is_uploaded_file($_FILES['naslovna']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['naslovna']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
                $error = 'Naslovna slika: dozvoljeno jpg/jpeg/png/webp.';
            } else {
                $fn = 'cover_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $uploadDirCover . '/' . $fn;
                if (move_uploaded_file($_FILES['naslovna']['tmp_name'], $dest)) {
                    $naslovnaPath = 'assets/uploads/news/' . $fn;
                }
            }
        }

        if ($error === '') {
            if ($editing) {
                $stmt = $conn->prepare("UPDATE vijesti SET naslov = ?, tekst = ?, naslovna_slika = ? WHERE id = ?");
                $stmt->bind_param("sssi", $naslov, $tekst, $naslovnaPath, $id);
                $stmt->execute();
                $stmt->close();
                $vijestId = $id;
            } else {
                $odobreno = ($role === 'administrator') ? 1 : 0;
                $stmt = $conn->prepare("INSERT INTO vijesti (korisnik_id, naslov, tekst, naslovna_slika, odobreno) VALUES (?,?,?,?,?)");
                $stmt->bind_param("isssi", $me, $naslov, $tekst, $naslovnaPath, $odobreno);
                $stmt->execute();
                $vijestId = (int)$stmt->insert_id;
                $stmt->close();
            }

            if (!empty($_FILES['slike']['name'][0])) {
                for ($i = 0; $i < count($_FILES['slike']['name']); $i++) {
                    if (!is_uploaded_file($_FILES['slike']['tmp_name'][$i])) continue;

                    $ext = strtolower(pathinfo($_FILES['slike']['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) continue;

                    $fn = 'img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $dest = $uploadDirGallery . '/' . $fn;

                    if (move_uploaded_file($_FILES['slike']['tmp_name'][$i], $dest)) {
                        $path = 'assets/uploads/news/gallery/' . $fn;
                        $stmt = $conn->prepare("INSERT INTO vijesti_slike (vijest_id, putanja) VALUES (?,?)");
                        $stmt->bind_param("is", $vijestId, $path);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }

            header('Location: index.php?menu=13');
            exit;
        }
    }
}

$naslovVal = $vijest['naslov'] ?? '';
$tekstVal = $vijest['tekst'] ?? '';

?>
<section>
  <h2><?= $editing ? 'Uredi vijest' : 'Nova vijest' ?></h2>

  <?php if ($error !== ''): ?>
    <p style="color:#ffd2d2; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Naslov</label>
    <input type="text" name="naslov" value="<?= htmlspecialchars($naslovVal) ?>" required>

    <label>Tekst</label>
    <textarea name="tekst" rows="8" required><?= htmlspecialchars($tekstVal) ?></textarea>

    <label>Naslovna slika (jpg/jpeg/png/webp)</label>
    <input type="file" name="naslovna" accept=".jpg,.jpeg,.png,.webp">

    <label>Galerija slika (može više)</label>
    <input type="file" name="slike[]" multiple accept=".jpg,.jpeg,.png,.webp">

    <button type="submit">Spremi</button>
  </form>
</section>
