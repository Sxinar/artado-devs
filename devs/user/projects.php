<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login");
  exit();
}


// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login");
  exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının projeleri ve her projeye ait ilk resmi çek
$stmt = $db->prepare("
    SELECT p.id, p.title, p.description, p.features, p.upload_date, pi.image_path
    FROM projects p
    LEFT JOIN project_images pi ON p.id = pi.project_id
    WHERE p.user_id = :user_id
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<br>
<main>
<div class="min-h-screen bg-black text-white">
  <div class="container mx-auto py-8 px-6">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-4xl font-semibold tracking-wide border-b border-white pb-2">Projelerim</h1>
      <a href="upload" class="bg-transparent border-2 border-white text-white font-semibold rounded-lg py-2 px-4 hover:bg-white hover:text-black transition shadow-sm">
        Yeni Proje Yükle
      </a>
    </div>

    <?php if (count($projects) > 0): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($projects as $project): ?>
          <div class="bg-black border border-white rounded-lg shadow-sm overflow-hidden transition-transform transform hover:scale-105">
            <!-- Proje Resmi -->
            <?php if (!empty($project['image_path']) && file_exists($project['image_path'])): ?>
              <img src="<?php echo htmlspecialchars($project['image_path']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="w-full h-48 object-cover">
            <?php else: ?>
              <img src="default-image.jpg" alt="Default Image" class="w-full h-48 object-cover">
            <?php endif; ?>

            <!-- Proje Bilgileri -->
            <div class="p-4">
              <h2 class="text-xl font-semibold text-white border-b border-white pb-2"><?php echo htmlspecialchars($project['title']); ?></h2>
              <p class="text-gray-300 text-sm mt-2"><?php echo htmlspecialchars($project['description']); ?></p>
              <p class="text-gray-400 text-xs mt-2">Yüklenme Tarihi: <?php echo date("d-m-Y", strtotime($project['upload_date'])); ?></p>
              
              <?php if (!empty($project['features'])): ?>
                <p class="text-gray-400 text-xs mt-1">Özellikler: <?php echo htmlspecialchars($project['features']); ?></p>
              <?php endif; ?>
            </div>

            <!-- Proje Aksiyonları -->
            <div class="bg-black text-white p-4 flex justify-between items-center border-t border-white">
              <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="text-white hover:text-gray-400 font-semibold transition">Düzenle</a>
              <a href="delete_projects.php?id=<?php echo $project['id']; ?>" class="text-white hover:text-gray-400 font-semibold transition">Sil</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="bg-black border border-white text-gray-400 p-6 rounded-lg text-center">
        Henüz bir proje yüklemediniz.
      </div>
    <?php endif; ?>
  </div>
</div>

  </main>
  <script>
  // Sidebar'ı açma/kapama fonksiyonu
  function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open'); // Sidebar'a 'open' sınıfını ekleyip kaldırıyoruz
  }
</script>

</body>

</html>