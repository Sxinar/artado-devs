<?php
require_once 'includes/database.php';

if (isset($_GET['token'])) {
  $token = $_GET['token'];

  // Jetonun veritabanında olup olmadığını ve geçerlilik süresinin dolup dolmadığını kontrol et
  $stmt = $db->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
  $stmt->bindParam(':token', $token);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $new_password = $_POST['new_password'];
      $new_password_confirm = $_POST['new_password_confirm'];

      // Şifre doğrulama ve eşleşme kontrolleri
      if ($new_password !== $new_password_confirm) {
        $error = "Şifreler eşleşmiyor.";
      } else {
        // Yeni şifreyi veritabanına kaydet
        try {
          $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
          $stmt = $db->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :user_id");
          $stmt->bindParam(':password', $hashed_password);
          $stmt->bindParam(':user_id', $user['id']);
          $stmt->execute();

          // Şifre güncelleme başarılı, giriş sayfasına yönlendir
          header("Location: login.php");
          exit();
        } catch (PDOException $e) {
          $error = "Şifre güncellenirken bir hata oluştu.";
        }
      }
    }
  } else {
    $error = "Geçersiz veya süresi dolmuş jeton.";
  }
} else {
  $error = "Geçersiz jeton.";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Şifre Sıfırla</title>
  <link rel="icon" type="image/x-icon" href="/public/uploads/files/favicon.ico">
  <link href="public/css/style.css" rel="stylesheet">
</head>
<body>

  <main>
    <section class="reset-password">
      <h2>Şifre Sıfırla</h2>
      <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
      <form method="post">
        <div>
          <label for="new_password">Yeni Şifre:</label>
          <input type="password" id="new_password" name="new_password" required>
        </div>
        <div>
          <label for="new_password_confirm">Yeni Şifre Tekrar:</label>
          <input type="password" id="new_password_confirm" name="new_password_confirm" required>
        </div>
        <button type="submit">Şifreyi Sıfırla</button>
      </form>
    </section>
  </main>

</body>
</html>