<?php
require_once 'includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password_confirm = $_POST['cpass'];
  $website = $_POST['website'];

  // Basit hata kontrolleri
  if ($password !== $password_confirm) {
    $error = "Şifreler eşleşmiyor.";
  } else {
    // Kullanıcıyı veritabanına kaydet
    try {
      $stmt = $db->prepare("INSERT INTO users (username, email, password, website) VALUES (:username, :email, :password, :website)");
      $stmt->bindParam(':username', $username);
      $stmt->bindParam(':email', $email);  

      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt->bindParam(':password', $hashed_password);
      $stmt->bindParam(':website', $website);
      
      $stmt->execute();  

      // Kayıt başarılı, kullanıcıyı /user sayfasına yönlendir
      header("Location: /user");
      exit();
    } catch (PDOException $e) {
      if ($e->getCode() == 23000) {
        $error = "Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.";
      } else {
        $error = "Kayıt sırasında bir hata oluştu.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/public/uploads/files/favicon.ico">
  <style>
    /* fonts  */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #1c1c1c;
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
    }

    .container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 10px; /* Eklenen padding, mobilde daha iyi görünüm sağlar */
    }

    .box {
      background: #fdfdfd;
      display: flex;
      flex-direction: column;
      padding: 35px 20px; /* Daha küçük ekranlar için padding azaltıldı */
      border-radius: 20px;
      box-shadow: 0 0 128px 0 rgba(0, 0, 0, 0.1), 0 32px 64px -48px rgba(0, 0, 0, 0.5);
      width: 100%; /* Mobilde tam genişlik */
      max-width: 500px; /* Daha büyük ekranlarda genişliği sınırla */
    }

    .form-box {
      margin: 20px auto; /* Mobil için yukarıdan aşağıdan boşluk */
    }

    .form-box header {
      font-size: 30px;
      font-weight: 600;
      text-align: center;
      color: #2B547E;
    }

    .form-box hr {
      background-color: #2B547E;
      height: 4px;
      width: 20%;
      border: none;
      margin: 5px auto;
      outline: 0;
      border-radius: 5px;
    }

    .input-container {
      display: flex;
      width: 100%;
      margin-bottom: 15px;
      flex-direction: row;
    }

    .icon {
      padding: 15px;
      background: transparent;
      color: #555;
      background-color: #f1f1f1;
      min-width: 50px;
      text-align: center;
      cursor: pointer;
    }

    .input-field {
      width: 100%;
      padding: 10px;
      height: 50px;
      outline: none;
      border: none;
      font-size: 15px;
      background-color: #f1f1f1;
    }

    .input-field:focus {
      color: #2B547E;
    }

    .remember {
      display: flex;
      font-size: 15px;
      margin-bottom: 20px; /* Mobil için azaltıldı */
      margin-top: 20px;
    }

    .btn {
      height: 45px;
      width: 100%; /* Tam genişlik */
      max-width: 300px;
      background-color: #262626;
      border: 0;
      border-radius: 5px;
      color: #fff;
      font-size: 18px;
      cursor: pointer;
      transition: all 0.3s;
      padding: 0 15px;
      margin: 10px auto; /* Ortala */
    }

    .btn:hover {
      opacity: 0.7;
    }

    .links {
      margin: 10px;
      text-align: center;
    }

    .links a {
      text-decoration: none;
      color: #2B547E;
    }

    .links a:hover {
      font-weight: bold;
    }

    @media only screen and (max-width: 768px) {
      .form-box header {
        font-size: 24px; /* Başlık boyutu küçültüldü */
      }

      .btn {
        width: 100%; /* Daha dar ekranlarda tam genişlik */
      }

      .input-container {
        flex-direction: column; /* Mobilde dikey hizalama */
      }

      .icon {
        min-width: auto;
        padding: 10px;
      }

      .input-field {
        width: 100%;
      }
    }

    @media only screen and (max-width: 480px) {
      .form-box {
        padding: 20px 15px; /* En dar ekranlar için padding azaltıldı */
      }

      .box {
        border-radius: 10px; /* Daha küçük radius */
      }

      .btn {
        font-size: 16px; /* Düğme boyutu küçültüldü */
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="form-box box">
      <header>Kayıt Ol</header>
      <hr>

      <?php if (isset($error)): ?>
      <div class="error text-center p-2 bg-red-100 rounded">
        <?php echo $error; ?>
      </div>
      <?php endif; ?>

      <form method="post">
        <div class="form-box">
          <div class="input-container">
            <i class="fa fa-user icon"></i>
            <input class="input-field" type="text" placeholder="Kullanıcı Adı" id="username" name="username" required>
          </div>
          <div class="input-container">
            <i class="fa fa-envelope icon"></i>
            <input class="input-field" type="email" placeholder="Email" id="email" name="email" required>
          </div>
          <div class="input-container">
            <i class="fa fa-user icon"></i>
            <input type="url" id="website" name="website" class="input-field" placeholder="https://example.com">
          </div>
          <div class="input-container">
            <i class="fa fa-lock icon"></i>
            <input class="input-field password" type="password" placeholder="Şifre" id="password" name="password" required>
            <i class="fa fa-eye icon toggle"></i>
          </div>
          <div class="input-container">
            <i class="fa fa-lock icon"></i>
            <input class="input-field" type="password" id="password_confirm" placeholder="Şifrenizi Onaylayın" name="cpass"
              required>
            <i class="fa fa-eye icon toggle"></i>
          </div>
        </div>

        <center>
          <button type="submit" class="btn">Kayıt Ol</button>
        </center>

        <div class="links">
          Hesabınız var mı? <a href="login.php">Giriş Yap</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const toggles = document.querySelectorAll(".toggle");
    toggles.forEach(toggle => {
      toggle.addEventListener("click", (e) => {
        const input = e.target.previousElementSibling;
        if (input.type === "password") {
          input.type = "text";
          toggle.classList.replace("fa-eye-slash", "fa-eye");
        } else {
          input.type = "password";
        }
      });
    });
  </script>
</body>

</html>
