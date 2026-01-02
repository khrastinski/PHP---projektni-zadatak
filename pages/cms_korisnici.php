<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

require_role(['administrator']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $rola = $_POST['user_rola'] ?? 'user';
    $user_cms = isset($_POST['user_cms']) ? 1 : 0;
    $aktivan = isset($_POST['aktivan']) ? 1 : 0;

   
    $allowed = ['user','editor','administrator'];
    if (!in_array($rola, $allowed, true)) {
        $rola = 'user';
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE korisnici SET user_rola=?, user_cms=?, aktivan=? WHERE id=?");
        $stmt->bind_param("siii", $rola, $user_cms, $aktivan, $id);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: index.php?menu=12');
    exit;
}


$res = $conn->query("SELECT id, ime, prezime, email, user_rola, user_cms, aktivan FROM korisnici ORDER BY id DESC");
$korisnici = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<section>
  <h2>Upravljanje korisnicima</h2>

  <table border="1" cellpadding="8" cellspacing="0">
    <tr>
      <th>ID</th><th>Ime</th><th>Prezime</th><th>Email</th>
      <th>Rola</th><th>CMS</th><th>Aktivan</th><th>Spremi</th>
    </tr>

    <?php foreach ($korisnici as $k): ?>
      <tr>
        <form method="post" action="index.php?menu=12">
          <td>
            <?= (int)$k['id'] ?>
            <input type="hidden" name="id" value="<?= (int)$k['id'] ?>">
          </td>
          <td><?= htmlspecialchars($k['ime']) ?></td>
          <td><?= htmlspecialchars($k['prezime']) ?></td>
          <td><?= htmlspecialchars($k['email']) ?></td>

          <td>
            <select name="user_rola">
              <?php foreach (['user','editor','administrator'] as $r): ?>
                <option value="<?= $r ?>" <?= ($k['user_rola'] === $r ? 'selected' : '') ?>>
                  <?= htmlspecialchars($r) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>

          <td style="text-align:center;">
            <input type="checkbox" name="user_cms" <?= ((int)$k['user_cms'] === 1 ? 'checked' : '') ?>>
          </td>

          <td style="text-align:center;">
            <input type="checkbox" name="aktivan" <?= ((int)$k['aktivan'] === 1 ? 'checked' : '') ?>>
          </td>

          <td><button type="submit">Spremi</button></td>
        </form>
      </tr>
    <?php endforeach; ?>
  </table>
</section>
