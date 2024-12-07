<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar</title>
    <link rel="icon" type="image/x-icon" href="../public/uploads/files/favicon.ico">
</head>
<body>
    
</body>
</html>

<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login");
  exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Profil bilgilerini güncelle
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Profil fotoğrafını güncelle
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../public/uploads/pp/";
            $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Dosya türü kontrolü
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif") {
                $error = "Sadece JPG, JPEG, PNG & GIF dosyaları desteklenir.";
            } else {
                if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                    // Resmi veritabanına kaydet
                    try {
                        $stmt = $db->prepare("UPDATE users SET profile_photo = :profile_photo WHERE id = :user_id");
                        $stmt->bindParam(':profile_photo', $target_file);
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->execute();
                        $success = "Profil fotoğrafı güncellendi.";
                    } catch (PDOException $e) {
                        $error = "Profil fotoğrafı güncellenirken bir hata oluştu.";
                    }
                } else {
                    $error = "Dosya yüklenirken bir hata oluştu.";
                }
            }
        }

        try {
            $stmt = $db->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $success = "Profil bilgileri güncellendi.";
        } catch (PDOException $e) {
            $error = "Profil bilgileri güncellenirken bir hata oluştu.";
        }

    } elseif (isset($_POST['change_password'])) {
        // Şifreyi değiştir
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $new_password_confirm = $_POST['new_password_confirm'];

        // Mevcut şifreyi kontrol et
        $stmt = $db->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            // Şifre doğrulama ve eşleşme kontrolleri
            if ($new_password !== $new_password_confirm) {
                $error = "Şifreler eşleşmiyor.";
            } elseif (strlen($new_password) < 8) {
                $error = "Şifre en az 8 karakter olmalıdır.";
            } else {
                // Yeni şifreyi veritabanına kaydet
                try {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :user_id");
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->execute();
                    $success = "Şifre değiştirildi.";
                } catch (PDOException $e) {
                    $error = "Şifre değiştirilirken bir hata oluştu.";
                }
            }
        } else {
            $error = "Mevcut şifre yanlış.";
        }

    }
}



// Kullanıcı bilgilerini veritabanından çek
$stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<?php require_once '../header.php'; ?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="container mx-auto mt-12 p-8 max-w-2xl">
    <!-- Başlık Alanı -->
    <div class="bg-gradient-to-r from-blue-700 via-purple-600 to-purple-500 text-white py-8 px-6 rounded-lg shadow-lg">
        <h1 class="text-4xl font-extrabold text-center mb-2">Ayarlar</h1>
        <p class="text-lg text-center font-medium opacity-90">Hesap bilgilerinizi ve şifrenizi kolayca güncelleyin</p>
    </div>

    <!-- Profil Bilgileri -->
    <div class="bg-white mt-8 p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Profil Bilgileri</h2>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                <strong class="font-bold">Hata!</strong>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="username" class="block text-lg font-semibold text-gray-700 mb-1">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div>
                <label for="email" class="block text-lg font-semibold text-gray-700 mb-1">E-posta:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div>
                <label for="profile_photo" class="block text-lg font-semibold text-gray-700 mb-1">Profil Fotoğrafı:</label>
                <input type="file" id="profile_photo" name="profile_photo"
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div class="text-right">
                <button type="submit" name="update_profile"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-500 text-white font-bold text-lg rounded-lg shadow-lg hover:opacity-90">
                    Bilgileri Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- Şifre Değiştir -->
    <div class="bg-white mt-8 p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Şifre Değiştir</h2>
        <form method="post" class="space-y-6">
            <div>
                <label for="current_password" class="block text-lg font-semibold text-gray-700 mb-1">Mevcut Şifre:</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div>
                <label for="new_password" class="block text-lg font-semibold text-gray-700 mb-1">Yeni Şifre:</label>
                <input type="password" id="new_password" name="new_password" required
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div>
                <label for="new_password_confirm" class="block text-lg font-semibold text-gray-700 mb-1">Yeni Şifre Tekrar:</label>
                <input type="password" id="new_password_confirm" name="new_password_confirm" required
                       class="w-full mt-1 p-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>
            <div class="text-right">
                <button type="submit" name="change_password"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-500 text-white font-bold text-lg rounded-lg shadow-lg hover:opacity-90">
                    Şifreyi Değiştir
                </button>
            </div>
        </form>
    </div>
</div>


<?php require_once '../footer.php'; ?>