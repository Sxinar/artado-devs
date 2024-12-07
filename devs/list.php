<?php
require_once 'includes/database.php';


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $project_id = $_GET['id'];

  try {
    $stmt = $db->prepare("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = :project_id");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
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

try {
  $stmt = $db->query("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 6");
  $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (!$projects) {
      $projects = [];
  }
} catch (PDOException $e) {
  echo "Veritabanı hatası: " . $e->getMessage();
  $projects = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artado Devs</title>  

    <link rel="icon" type="image/x-icon" href="/public/uploads/files/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>  

        /* Sayfa düzeni için gerekli stil */
        .u-footer .u-sheet-1 {
            min-height: 120px;
        }

        .u-footer .u-social-icons-1 {
            height: 54px;
            min-height: 16px;
            width: 182px;
            min-width: 68px;
            white-space: nowrap;
            margin: 33px auto;
        }

        .u-footer .u-icon-1 {
            height: 32px;
        }

        .u-footer .u-icon-2 {
            height: 54px;
        }

        .u-footer .u-icon-3 {
            height: 32px;
        }

        @media (max-width: 1199px) {
            .u-footer .u-sheet-1 {
                min-height: 99px;
            }

            .u-footer .u-social-icons-1 {
                width: 158px;
                min-width: 94px;
            }
        }

        @media (max-width: 991px) {
            footer {
                min-height: 76px;
            }
        }

        @media (max-width: 767px) {
            footer {
                min-height: 57px;
            }
        }

        @media (max-width: 575px) {
            footer {
                min-height: 36px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-blue-600 text-white p-4 shadow">
    <h1 class="text-center text-3xl font-bold">Yüklenmiş Projeler</h1>
</header>

<main class="container mx-auto px-4 py-8">
    <section class="projects mt-5">
        <h2 class="text-2xl font-bold text-gray-800">Son Eklenen Projeler</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <?php
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    // Proje açıklamasını sınırlamak için $short_description değişkenini tanımlayın
                    $description = $project['description'];
                    $wrapped_description = wordwrap($description, 100);
                    $short_description = substr($wrapped_description, 0, 100);
                    if (strlen($wrapped_description) > 100) {
                        $short_description .= "...";
                    }

                    // Proje resmini çekme işlemi
                    $stmt = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :project_id LIMIT 1");
                    $stmt->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $image = $stmt->fetch(PDO::FETCH_ASSOC);

                    $image_path = isset($image['image_path']) ? $image['image_path'] : 'placeholder.jpg';

                    echo '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
                    echo '<img src="' . $image_path . '" alt="Proje Resmi" class="w-full h-48 object-cover">';
                    echo '<div class="p-4">';
                    echo '<h3 class="text-xl font-semibold mb-2">' . $project['title'] . '</h3>';
                    echo '<p class="text-gray-600 text-sm">Yükleyen: ' . $project['username'] . '</p>';
                    echo '<p class="text-gray-600 text-sm line-clamp-3">' . $short_description . '</p>';
                    echo '<a href="project.php?id=' . $project['id'] . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block mt-4">Detaylar</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-600">Gösterilecek proje bulunmamaktadır.</p>';
            }
            ?>

        </div>
    </section>
</main>