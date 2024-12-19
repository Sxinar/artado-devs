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
  <title>Profil</title>
  <link rel="icon" href="../uploads/logo.png" type="image/x-icon">
  <!-- TailwindCSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="/css/admin.css">

  <style>
    /* Koyu Siyah Tema */
    :root {
      --pure-black: #0a0a0a;
      --pure-white: #ffffff;
      --dark-gray: #222222;
      --light-gray: #b5b5b5;
      --soft-gray: #333333;
      --border-color: rgba(255, 255, 255, 0.1);
      --hover-color: rgba(255, 255, 255, 0.3);
    }

    /* Body ve Genel Düzen */
    html,
    body {
      height: 100%;
      margin: 0;
      background-color: var(--pure-black);
      color: var(--pure-white);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Header */
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: var(--dark);
      color: var(--pure-white);
      padding: 1rem 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
      z-index: 1000;
    }

    header h1 {
      margin: 0;
      font-size: 1.8rem;
      font-weight: 600;
    }

    /* Sidebar */
    /* Sidebar: Profil kısmını navbar'a yaklaştırma */
    .sidebar {
      position: fixed;
      top: 20px;
      /* Navbar'ın hemen altına almak için 60px yerine 40px olarak ayarlandı */
      left: 0;
      width: 280px;
      height: 100vh;
      background-color: var(--dark);
      padding: 2rem;
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      border-radius: 0 10px 10px 0;
      z-index: 999;
      overflow-y: auto;
    }


    .sidebar h2 {
      color: var(--pure-white);
      margin-bottom: 2rem;
      font-size: 1.5rem;
    }

    .sidebar .menu-item {
      color: var(--pure-white);
      margin: 1.2rem 0;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      transition: color 0.3s ease;
    }

    .sidebar .menu-item:hover {
      color: var(--hover-color);
      transform: translateX(5px);
    }

    .sidebar .menu-item .material-icons {
      margin-right: 10px;
      font-size: 1.2rem;
    }

    /* Profil Bölümü */
    .profile-section {
      margin-top: auto;
      padding: 1.5rem;
      background-color: var(--dark);
      border-top: 1px solid var(--border-color);
      border-radius: 0 0 10px 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
    }

    .profile-section:hover {
      background-color: var(--soft-gray);
    }

    .profile-section img {
      border-radius: 50%;
      border: 3px solid var(--border-color);
      width: 50px;
      height: 50px;
      margin-right: 15px;
    }

    .profile-section .username {
      color: var(--pure-white);
      font-weight: 600;
      font-size: 1.2rem;
    }

    .profile-section .username:hover {
      color: var(--hover-color);
    }

    /* Main Content */
    main {
      margin-left: 280px;
      padding: 2rem;
      overflow-y: auto;
      flex: 1;
      transition: margin-left 0.3s ease;
      margin-top: 60px;

    }


    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Profil Bölümü */
    .profile {
      background-color: var(--dark-gray);
      border: 1px solid var(--light-gray);
      border-radius: 16px;
      padding: 2rem;
      margin-top: 3.5rem;
    }

    .profile-info {
      border-bottom: 1px solid var(--light-gray);
      padding-bottom: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .profile-info img {
      border-radius: 50%;
      border: 4px solid var(--border-color);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    }

    h2.text-2xl {
      color: var(--pure-white);
      font-weight: 600;
      margin-bottom: 1.5rem;
    }

    .text-gray-600 {
      color: rgba(255, 255, 255, 0.7);
    }

    /* Blog Yazıları Alanı */
    .blog-section {
      background-color: var(--dark-gray);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      margin-top: 2rem;
      padding: 2rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    .blog-item {
      margin-bottom: 1.5rem;
    }

    .blog-item a {
      color: var(--pure-white);
      font-weight: 600;
      text-decoration: none;
      font-size: 1.2rem;
    }

    .blog-item a:hover {
      color: var(--hover-color);
    }

    .blog-item p {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.95rem;
      margin-top: 0.5rem;
    }

    /* Projeler Alanı */
    .project-section {
      background-color: var(--dark-gray);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 2rem;
      margin-top: 2rem;
    }

    .project-section ul {
      list-style-type: none;
      padding-left: 0;
    }

    .project-section ul li {
      color: var(--pure-white);
      font-size: 1.1rem;
      margin-bottom: 1rem;
    }

    .project-section ul li span {
      color: var(--light-gray);
      font-weight: normal;
    }

    /* Butonlar */
    .btn {
      background-color: var(--dark-gray);
      border: 1px solid var(--border-color);
      color: var(--pure-white);
      padding: 0.8rem 1.5rem;
      border-radius: 12px;
      transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
      display: inline-block;
      text-align: center;
      margin-top: 1rem;
    }

    .btn:hover {
      background-color: var(--hover-color);
      color: var(--pure-white);
      transform: scale(1.05);
    }

    .btn-primary {
      background-color: var(--dark-gray);
      margin-right: 1rem;
    }

    .btn-secondary {
      background-color: transparent;
    }

    /* Responsive Tasarım */
    @media (max-width: 768px) {
      .sidebar {
        width: 230px;
      }

      main {
        margin-left: 230px;
      }

      header {
        padding: 1rem;
      }

      .sidebar h2 {
        font-size: 1.3rem;
      }

      .sidebar .menu-item {
        font-size: 0.9rem;
        margin: 0.8rem 0;
      }
    }

    @media (max-width: 480px) {
      .sidebar {
        width: 0;
        display: none;
      }

      main {
        margin-left: 0;
      }

      header {
        padding: 0.8rem;
      }
    }

    .text-xl font-semibold {

      color: #000000;
    }


    /* Sol Menü Profil Alanı */
    .side-profile {
      margin-top: -20px;
      /* Profil kısmını yukarı kaydırmak için */
      padding: 1.5rem;
      background-color: var(--dark-gray);
      border-radius: 10px;
      color: var(--pure-white);
    }

    .side-profile .info {
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .side-profile .info img {
      border-radius: 50%;
      width: 64px;
      height: 64px;
      margin-bottom: 10px;
    }

    .side-profile .info p {
      font-size: 0.9rem;
      color: var(--light-gray);
      margin-top: 5px;
    }

    /* Mobil Menü Butonu */
.mobile-menu-toggle {
  display: none;
  position: fixed;
  top: 20px;
  left: 20px;
  background-color: var(--dark-gray);
  border-radius: 50%;
  padding: 10px;
  z-index: 1001;
  cursor: pointer;
}

.mobile-menu-toggle i {
  color: var(--pure-white);
  font-size: 1.8rem;
}

@media (max-width: 768px) {
  /* Mobil Görünümde Menü Butonunu Göster */
  .mobile-menu-toggle {
    display: block;
  }
  
  /* Sidebar'ın gizlenmesi ve açılması */
  .sidebar {
    left: -280px; /* Başlangıçta sidebar gizli */
    transition: left 0.3s ease;
  }

  .sidebar.open {
    left: 0; /* Sidebar açıldığında */
  }
}

/* Duyuru Bölümü */
/* Duyuru Bölümü */
.announcement {
  background-color: #e74c3c; /* Kırmızı renk */
  color: var(--pure-white);
  padding: 1.5rem;
  margin-top: 60px; /* Header'ın altına yerleştirebilmek için margin ekledik */
  margin-bottom: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.announcement-content h3 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.announcement-content p {
  font-size: 1rem;
}

/* Mobilde Duyuru Bölümünün Boyutlarını Ayarlama */
@media (max-width: 768px) {
  .announcement {
    padding: 1rem;
    margin-bottom: 1.5rem;
    margin-top: 50px; /* Mobilde de header'ın altına uygun boşluk ekliyoruz */
  }

  .announcement-content h3 {
    font-size: 1.2rem;
  }

  .announcement-content p {
    font-size: 0.9rem;
  }
}



  </style>

</head>

<body class="bg-black text-white">
  <?php require_once '../header.php'; ?>

  <!-- Sol Menü -->
  <div class="sidebar">
    <!-- Mobil Menü Açma Butonu -->



    <hr>
    <div class="side-nav">
      <div class="item">
        <i class='bx bx-search-alt'></i>
        <a href="/user/">Ana Sayfa</a>
      </div>
      <div class="item">
        <i class='bx bx-message-square-dots'></i>
        <a href="duyuru.php">Duyurular</a>
      </div>
      <div class="item active">
        <i class='bx bx-briefcase'></i>
        <a href="projects">Projelerim</a>
      </div>
      <div class="item">
        <i class='bx bx-bookmark-minus'></i>
        <a href="https://forum.artado.xyz">Forum</a>
      </div>
      <div class="item">
        <i class='bx bx-cog'></i>
        <a href="settings">Ayarlar</a>
      </div>
    </div>

    <div class="side-profile">
      <div class="info">
        <img src="<?php echo $user['profile_photo']; ?>" alt="Profil Fotoğrafı" class="rounded-full w-16 h-16">
        <a class="text-xl font-semibold" style="  color: #000000;"><?php echo $user['username']; ?></a>
        <p>Beta Kullanıcısı</p>
      </div>
      <hr>
      <button><a href="../logout.php">Çıkış Yap</a></button>
    </div>
<!-- Mobil Menü Açma Butonu -->
<div class="mobile-menu-toggle" onclick="toggleSidebar()">
  <i class="bx bx-menu"></i>
</div>

  </div>

  </nav>

<main>
<main class="container mt-5">

    <section class="upload bg-dark-gray p-5 rounded-lg shadow-lg">

    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php elseif (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>


      <form method="post" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="title" class="form-label text-white">Proje Başlığı:</label>
          <input type="text" id="title" name="title" class="form-control bg-dark text-white border-0 rounded-md shadow-md" required>
        </div>

        <div class="mb-4">
          <label for="description" class="form-label text-white">Açıklama:</label>
          <textarea id="description" name="description" class="form-control bg-dark text-white border-0 rounded-md shadow-md"></textarea>
        </div>

        <div class="mb-4">
          <label for="version" class="form-label text-white">Versiyon:</label>
          <input type="text" id="version" name="version" class="form-control bg-dark text-white border-0 rounded-md shadow-md">
        </div>

        <div class="mb-4">
          <label for="features" class="form-label text-white">Özellikler: (Boşluklar ile "," kullanarak özellikleri ayırın)</label>
          <input type="text" id="features" name="features[]" placeholder="Örneğin: Açık kaynak, Özgür yazılım" class="form-control bg-dark text-white border-0 rounded-md shadow-md">
        </div>

        <div class="mb-4">
          <label for="category" class="form-label text-white">Kategori:</label>
          <select id="category" name="category" class="form-select bg-dark text-white border-0 rounded-md shadow-md" required>
            <option value="mobil_oyun">Mobil Oyun</option>
            <option value="pc_oyun">PC Oyun</option>
            <option value="mobil_uygulama">Mobil Uygulama</option>
            <option value="pc_uygulama">PC Uygulama</option>
            <option value="artado_eklenti">Artado Eklenti</option>
            <option value="artado_tema">Artado Tema</option>
            <option value="artado_logo">Artado Logo</option>
          </select>
        </div>

        <div class="mb-4">
          <label for="file" class="form-label text-white">Proje Dosyası:</label>
          <input type="file" id="file" name="file" class="form-control bg-dark text-white border-0 rounded-md shadow-md" required>
        </div>

        <div class="mb-4">
          <label for="image" class="form-label text-white">Proje Resmi:</label>
          <input type="file" id="image" name="image" class="form-control bg-dark text-white border-0 rounded-md shadow-md">
        </div>

        <button type="submit" class="btn btn-primary w-full py-3 mt-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 shadow-md transform transition duration-200 hover:scale-105">Yükle</button>
      </form>
    </section>
</main>

  </main>
  <script>
  // Sidebar'ı açma/kapama fonksiyonu
  function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open'); // Sidebar'a 'open' sınıfını ekleyip kaldırıyoruz
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>  


