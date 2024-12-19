<?php
require_once 'includes/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $project_id = $_GET['id'];

  try {
    // Proje bilgilerini al, project_link dahil
    $stmt = $db->prepare("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = :project_id");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
      // Proje resimlerini al
      $stmt = $db->prepare("SELECT * FROM project_images WHERE project_id = :project_id");
      $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
      $stmt->execute();
      $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $error = "Proje bulunamadı.";
    }
  } catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
  }
} else {
  $error = "Geçersiz proje ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="icon" type="image/x-icon" href="/public/uploads/files/favicon.ico">
  <title><?php echo isset($project['title']) ? $project['title'] : "Proje Detayları"; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Modal stili */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5); /* Yarı saydam arka plan */
    }
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      max-width: 90%;
      max-height: 80%;
    }
  </style>
</head>
<body class="bg-gray-100">

  <header class="bg-blue-500 py-4">
    <div class="container mx-auto px-4 text-white">
      <h1 class="text-2xl font-bold">
        <?php echo isset($project['title']) ? $project['title'] : "Proje Detayları"; ?>
      </h1>
    </div>
  </header>

  <main class="container mx-auto px-4 py-8">
    <section class="project bg-white p-6 rounded-lg shadow-md">
      <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <strong class="font-bold">Hata!</strong>
          <span class="block sm:inline"><?php echo $error; ?></span>
        </div>
      <?php else: ?>
        <div class="flex items-center mb-4">
          <div class="mr-4">
            <?php if (!empty($images)): ?>
              <img src="<?php echo $images[0]['image_path']; ?>" alt="Proje Resmi" class="w-24 h-24 rounded-full object-cover">
            <?php endif; ?>
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-800"><?php echo $project['title']; ?></h2>
            <p class="text-gray-600">Yükleyen: <?php echo $project['username']; ?></p>
          </div>
        </div>
        <p class="text-gray-700 mb-4"><?php echo $project['description']; ?></p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Özellikler:</h3>
            <ul class="list-disc list-inside text-gray-700">
              <?php 
              $features = explode(',', $project['features']);
              if (!empty($features[0])) {
                foreach ($features as $feature): ?>
                  <li><?php echo $feature; ?></li>
                <?php endforeach; 
              } else {
                echo "<li>Özellik bulunamadı.</li>";
              }
              ?>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Resimler:</h3>
            <div class="flex flex-wrap">
              <?php 
              if (!empty($images)) {
                foreach ($images as $image): ?>
                  <img src="<?php echo $image['image_path']; ?>" alt="Proje Resmi" class="w-20 h-20 rounded-md mr-2 mb-2 object-cover cursor-pointer" onclick="openModal('<?php echo $image['image_path']; ?>')">
                <?php endforeach; 
              } else {
                echo "<p class='text-gray-700'>Resim bulunamadı.</p>";
              }
              ?>
            </div>
          </div>
        </div>

        <!-- Eğer project_link sütunu varsa, göster -->
        <?php if (isset($project['project_link']) && !empty($project['project_link'])): ?>
          <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-800">Proje Linki:</h3>
            <a href="<?php echo $project['project_link']; ?>" class="text-blue-500 hover:underline" target="_blank">
              <?php echo $project['project_link']; ?>
            </a>
          </div>
        <?php endif; ?>

        <a href="<?php echo $project['file_path']; ?>" download class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block mt-4">
          Projeyi İndir
        </a>
      <?php endif; ?>
    </section>
  </main>

  <!-- Modal -->
  <div id="imageModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImage" src="" alt="Büyük Resim" class="w-full">
    </div>
  </div>



  <script>
    // Modal açma fonksiyonu
    function openModal(imageSrc) {
      document.getElementById("imageModal").style.display = "block";
      document.getElementById("modalImage").src = imageSrc;
    }

    // Modal kapama fonksiyonu
    function closeModal() {
      document.getElementById("imageModal").style.display = "none";
    }

    // Modal dışına tıklama ile kapama
    window.onclick = function(event) {
      if (event.target == document.getElementById("imageModal")) {
        closeModal();
      }
    }
  </script>

</body>
</html>
