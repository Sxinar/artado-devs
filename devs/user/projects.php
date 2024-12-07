<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

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

<?php require_once '../header.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

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

<?php require_once '../footer.php'; ?>
