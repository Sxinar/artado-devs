<?php
require_once 'includes/database.php';
require_once 'includes/auth.php';

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından çek
$stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<div class="profile-info flex items-center space-x-4">
        <img src="<?php echo $user['profile_photo']; ?>" alt="Profil Fotoğrafı" class="rounded-full w-16 h-16">
        <div>