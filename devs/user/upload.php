<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login");
  exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $version = $_POST['version'];
  $features = implode(',', $_POST['features']);
  $category = $_POST['category']; // Kategori seçimi

  // Dosya yükleme işlemleri
  if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../public/uploads/files/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
      try {
        $stmt = $db->prepare("INSERT INTO projects (user_id, title, description, version, features, file_path, category) 
                              VALUES (:user_id, :title, :description, :version, :features, :file_path, :category)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':version', $version);
        $stmt->bindParam(':features', $features);
        $stmt->bindParam(':file_path', $target_file);
        $stmt->bindParam(':category', $category); // Kategori veritabanına ekleniyor
        $stmt->execute();
        $project_id = $db->lastInsertId();

        // Proje resmi yükleme
        $image_upload_result = uploadProjectImage($project_id, $db);
        if ($image_upload_result !== true) {
          $error = $image_upload_result;
        } else {
          header("Location: projects");
          exit();
        }
      } catch (PDOException $e) {
        
      }
    } else {
      $error = "Dosya yüklenirken bir hata oluştu.";
    }
  } else {
    $error = "Lütfen bir proje dosyası seçin.";
  }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proje Yükle</title>
  <link href="../public/css/style.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="../public/uploads/files/favicon.ico">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>


  <main class="container mt-5">
    <section class="upload bg-light p-5 rounded shadow-sm">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="title" class="form-label">Proje Başlığı:</label>
          <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Açıklama:</label>
          <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
          <label for="version" class="form-label">Versiyon:</label>
          <input type="text" id="version" name="version" class="form-control">
        </div>

        <div class="mb-3">
          <label for="features" class="form-label">Özellikler:</label>
          <input type="text" id="features" name="features[]" class="form-control" multiple>
        </div>

        <div class="mb-3">
          <label for="category" class="form-label">Kategori:</label>
          <select id="category" name="category" class="form-select" required>
            <option value="mobil_oyun">Mobil Oyun</option>
            <option value="pc_oyun">PC Oyun</option>
            <option value="mobil_uygulama">Mobil Uygulama</option>
            <option value="pc_uygulama">PC Uygulama</option>
            <option value="artado_eklenti">Artado Eklenti</option>
            <option value="artado_tema">Artado Tema</option>
            <option value="artado_logo">Artado Logo</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="file" class="form-label">Proje Dosyası:</label>
          <input type="file" id="file" name="file" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="image" class="form-label">Proje Resmi:</label>
          <input type="file" id="image" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Yükle</button>
      </form>
    </section>
  </main>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
