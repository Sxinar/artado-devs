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

if (isset($_GET['id'])) {
    $project_id = $_GET['id'];
    // Proje bilgilerini veritabanından çek
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :project_id AND user_id = :user_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $error = "Proje bulunamadı veya bu projeyi düzenleme yetkiniz yok.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $version = $_POST['version'];
            $features = implode(',', $_POST['features']); // Özellikleri virgülle ayrılmış bir dize olarak birleştir

            try {
                // Projeyi veritabanında güncelle
                $stmt = $db->prepare("UPDATE projects SET title = :title, description = :description, version = :version, features = :features WHERE id = :project_id");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':version', $version);
                $stmt->bindParam(':features', $features);
                $stmt->bindParam(':project_id', $project_id);
                $stmt->execute();

                // Proje resmi yükleme
                $image_upload_result = uploadProjectImage($project_id, $db);
                if ($image_upload_result !== true && $image_upload_result !== false) { // False ise resim seçilmemiş demektir, hata vermemeli
                    $error = $image_upload_result;
                } else {
                    // Güncelleme başarılı, proje sayfasına yönlendir
                    header("Location: projects");
                    exit();
                }
            } catch (PDOException $e) {
                $error = "Proje güncellenirken bir hata oluştu.";
            }
        }
    }
} else {
    $error = "Geçersiz proje ID.";
}

?>

<?php require_once '../header.php'; ?>

<!-- Responsive ve siyah beyaz tasarım -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="container mx-auto mt-8">
    <div class="max-w-lg mx-auto bg-black text-white p-8 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-center text-white mb-6">Projeyi Düzenle</h2>

        <?php if (isset($error)): ?>
            <div class="bg-gray-800 border border-red-600 text-red-400 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Hata!</strong>
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($project)): ?>
            <form method="post" enctype="multipart/form-data">
                <!-- Başlık -->
                <div class="mb-6">
                    <label for="title" class="block text-white font-semibold mb-2">Proje Başlığı:</label>
                    <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required
                           class="shadow appearance-none border border-gray-600 rounded-lg w-full py-2 px-4 bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-400">
                </div>
                <!-- Açıklama -->
                <div class="mb-6">
                    <label for="description" class="block text-white font-semibold mb-2">Açıklama:</label>
                    <textarea id="description" name="description"
                              class="shadow appearance-none border border-gray-600 rounded-lg w-full py-2 px-4 bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-400"><?php echo $project['description']; ?></textarea>
                </div>
                <!-- Versiyon -->
                <div class="mb-6">
                    <label for="version" class="block text-white font-semibold mb-2">Versiyon:</label>
                    <input type="text" id="version" name="version" value="<?php echo $project['version']; ?>"
                           class="shadow appearance-none border border-gray-600 rounded-lg w-full py-2 px-4 bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-400">
                </div>
                <!-- Özellikler -->
                <div class="mb-6">
                    <label for="features" class="block text-white font-semibold mb-2">Özellikler:</label>
                    <input type="text" id="features" name="features[]" value="<?php echo $project['features']; ?>"
                           multiple
                           class="shadow appearance-none border border-gray-600 rounded-lg w-full py-2 px-4 bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-400">
                </div>
                <!-- Resim Yükle -->
                <div class="mb-6">
                    <label for="image" class="block text-white font-semibold mb-2">Yeni Proje Resmi:</label>
                    <input type="file" id="image" name="image"
                           class="shadow appearance-none border border-gray-600 rounded-lg w-full py-2 px-4 bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-400">
                </div>
                <!-- Güncelleme Butonu -->
                <div class="flex items-center justify-center">
                    <button type="submit"
                            class="bg-gray-700 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Güncelle
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
