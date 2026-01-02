<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';


require_role(['administrator']); 

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];

    if ($id === (int)($_SESSION['user_id'] ?? 0)) {
        header('Location: index.php?menu=8');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM korisnici WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: index.php?menu=8');
    exit;
}

$sql = "
    SELECT k.id, k.ime, k.prezime, k.email, k.rola, k.cms, k.odobren,
           d.naziv AS drzava
    FROM korisnici k
    JOIN drzave d ON k.drzava_id = d.id
    ORDER BY k.id DESC
";
$res = $conn->query($sql);
$korisnici = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<div class="admin-wrapper">
  <h2>Administracija - korisnici</h2>

  <table border="1" cellpadding="8">
    <tr>
      <th>ID</th>
      <th>Ime</th>
      <th>Prezime</th>
      <th>Email</th>
      <th>Država</th>
      <th>Rola</th>
      <th>CMS</th>
      <th>Odobren</th>
      <th>Akcije</th>
    </tr>

    <?php foreach ($korisnici as $k): ?>
      <tr>
        <td><?= (int)$k['id'] ?></td>
        <td><?= htmlspecialchars($k['ime']) ?></td>
        <td><?= htmlspecialchars($k['prezime']) ?></td>
        <td><?= htmlspecialchars($k['email']) ?></td>
        <td><?= htmlspecialchars($k['drzava']) ?></td>
        <td><?= htmlspecialchars($k['rola']) ?></td>
        <td><?= (int)$k['cms'] === 1 ? 'DA' : 'NE' ?></td>
        <td><?= (int)$k['odobren'] === 1 ? 'DA' : 'NE' ?></td>
        <td>
          <a href="index.php?menu=10&id=<?= (int)$k['id'] ?>">Uredi</a>
          |
          <a href="index.php?menu=8&action=delete&id=<?= (int)$k['id'] ?>"
             onclick="return confirm('Obrisati korisnika?')">Obriši</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
