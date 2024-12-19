<?php
session_start();

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Veritabanı bağlantısı (örnek için ayarlayın)
require_once '../includes/database.php';

// Hata mesajlarını saklamak için bir değişken
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $version = $_POST['version'] ?? '';
    $features = $_POST['features'] ?? '';
    $category = $_POST['category'] ?? '';
    
    $featuresList = implode(',', array_map('trim', explode(',', $features)));

    $filePath = '';
    $imagePath = '';

    // Dosya yükleme işlemi
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $uploadDir = '../public/uploads/files/';
        
        // Geçerli dosya türlerini kontrol et (örnek: sadece PDF ve TXT izin veriliyor)
        $allowedExtensions = ['zip', 'txt'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, $allowedExtensions)) {
            $filePath = $uploadDir . uniqid() . '-' . $fileName;
            if (!move_uploaded_file($fileTmpPath, $filePath)) {
                $errors[] = 'Dosya yükleme başarısız oldu.';
            }
        } else {
            $errors[] = 'Geçersiz dosya türü. Sadece PDF ve TXT yüklenebilir.';
        }
    }

    // Resim yükleme işlemi
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $uploadDir = '../public/uploads/images/';

        $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $allowedImageExtensions)) {
            $imagePath = $uploadDir . uniqid() . '-' . $imageName;
            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                $errors[] = 'Resim yükleme başarısız oldu.';
            }
        } else {
            $errors[] = 'Geçersiz resim türü. Sadece JPG, JPEG, PNG ve GIF yüklenebilir.';
        }
    }

    // Eğer hata yoksa veritabanına kaydet
    if (empty($errors)) {
        try {
            $query = "INSERT INTO projects (title, description, version, features, category, file_path, image_path, user_id) VALUES (:title, :description, :version, :features, :category, :file_path, :image_path, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':version' => $version,
                ':features' => $featuresList,
                ':category' => $category,
                ':file_path' => $filePath,
                ':image_path' => $imagePath,
                ':user_id' => $_SESSION['user_id']
            ]);

            header('Location: success.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Bir hata oluştu: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proje Yükle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5">Proje Yükle</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="title" class="block">Proje Başlığı</label>
                <input type="text" id="title" name="title" class="w-full p-2 text-black" required>
            </div>

            <div>
                <label for="description" class="block">Proje Açıklaması</label>
                <textarea id="description" name="description" class="w-full p-2 text-black" required></textarea>
            </div>

            <div>
                <label for="version" class="block">Sürüm</label>
                <input type="text" id="version" name="version" class="w-full p-2 text-black">
            </div>

            <div>
                <label for="features" class="block">Özellikler (virgülle ayırarak)</label>
                <input type="text" id="features" name="features" class="w-full p-2 text-black">
            </div>

            <div>
                <label for="category" class="block">Kategori</label>
                <select id="category" name="category" class="w-full p-2 text-black">
                    <option value="web">Web</option>
                    <option value="mobile">Mobil</option>
                    <option value="desktop">Masaüstü</option>
                </select>
            </div>

            <div>
                <label for="file" class="block">Dosya Yükle</label>
                <input type="file" id="file" name="file" class="w-full p-2">
            </div>

            <div>
                <label for="image" class="block">Resim Yükle (Opsiyonel)</label>
                <input type="file" id="image" name="image" class="w-full p-2">
            </div>

            <div>
                <button type="submit" class="bg-blue-500 p-2 rounded">Gönder</button>
            </div>
        </form>
    </div>
</body>
</html>
