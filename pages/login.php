<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';

    if ($email === '' || $lozinka === '') {
        $error = 'Upiši e-mail i lozinku.';
    } else {
        $stmt = $conn->prepare("
            SELECT id, ime, prezime, email, lozinka_hash, user_cms, user_rola, aktivan
            FROM korisnici
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res  = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if ($user && password_verify($lozinka, $user['lozinka_hash'])) {

            
            if ((int)$user['aktivan'] !== 1) {
                $error = 'Račun još nije odobren. Pričekaj administratora.';
            } else {
                $_SESSION['user_id']      = (int)$user['id'];
                $_SESSION['user_ime']     = (string)$user['ime'];
                $_SESSION['user_prezime'] = (string)$user['prezime'];

                $_SESSION['user_cms']  = (int)$user['user_cms'];
                $_SESSION['user_rola'] = (string)$user['user_rola'];

                header('Location: index.php?menu=1');
                exit;
            }

        } else {
            $error = 'Neispravan e-mail ili lozinka.';
        }
    }
}
?>

<section>
    <h2>Prijava</h2>

    <?php if ($error !== ''): ?>
        <p style="color:#ff6b6b; font-weight:700;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="index.php?menu=7">
        <label for="email">E-mail*</label>
        <input type="email" id="email" name="email" required>

        <label for="lozinka">Lozinka*</label>
        <input type="password" id="lozinka" name="lozinka" required>

        <button type="submit">Prijavi se</button>
    </form>
</section>
