<?php
require_cms();
require_once __DIR__ . '/../includes/db.php';

$me = (int)($_SESSION['user_id'] ?? 0);
$role = role();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($id > 0) {
        if ($action === 'delete') {
            if ($role === 'administrator') {
                $stmt = $conn->prepare("DELETE FROM vijesti WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: index.php?menu=13');
            exit;
        }

        if ($action === 'approve') {
            if ($role === 'administrator') {
                $stmt = $conn->prepare("UPDATE vijesti SET odobreno = 1 WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: index.php?menu=13');
            exit;
        }

        if ($action === 'archive') {
            if ($role === 'administrator' || $role === 'editor') {
                $stmt = $conn->prepare("UPDATE vijesti SET arhiva = 1 WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: index.php?menu=13');
            exit;
        }

        if ($action === 'unarchive') {
            if ($role === 'administrator' || $role === 'editor') {
                $stmt = $conn->prepare("UPDATE vijesti SET arhiva = 0 WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: index.php?menu=13');
            exit;
        }
    }
}

$sql = "
SELECT v.*, k.ime, k.prezime
FROM vijesti v
JOIN korisnici k ON k.id = v.korisnik_id
";
if ($role === 'user') {
    $sql .= " WHERE v.korisnik_id = " . (int)$me . " ";
}
$sql .= " ORDER BY v.id DESC";

$res = $conn->query($sql);
$vijesti = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<section>
  <h2>Dodavanje i odobravanje vijesti</h2>

  <p><a href="index.php?menu=14">+ Nova vijest</a></p>

  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Naslov</th>
        <th>Autor</th>
        <th>Datum</th>
        <th>Odobreno</th>
        <th>Arhiva</th>
        <th>Akcije</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($vijesti as $v): ?>
        <tr>
          <td><?= (int)$v['id'] ?></td>
          <td><?= htmlspecialchars($v['naslov']) ?></td>
          <td><?= htmlspecialchars($v['ime'] . ' ' . $v['prezime']) ?></td>
          <td><?= htmlspecialchars($v['datum_unosa']) ?></td>
          <td><?= (int)$v['odobreno'] ?></td>
          <td><?= (int)$v['arhiva'] ?></td>
          <td>
            <a href="index.php?menu=14&id=<?= (int)$v['id'] ?>">Uredi</a>

            <?php if ($role === 'admin'): ?>
              | <a href="index.php?menu=13&action=approve&id=<?= (int)$v['id'] ?>">Odobri</a>
              | <a href="index.php?menu=13&action=delete&id=<?= (int)$v['id'] ?>" onclick="return confirm('Obrisati vijest?')">Obriši</a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'editor'): ?>
              <?php if ((int)$v['arhiva'] === 0): ?>
                | <a href="index.php?menu=13&action=archive&id=<?= (int)$v['id'] ?>">Arhiviraj</a>
              <?php else: ?>
                | <a href="index.php?menu=13&action=unarchive&id=<?= (int)$v['id'] ?>">Vrati</a>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
