<?php
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Kullanıcının giriş yapmış olması gerekiyor
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login");
  exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından çek
$stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sayfası</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #0F172A;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: #E2E8F0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .glass-container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .profile-photo-wrapper {
            position: relative;
            width: fit-content;
        }

        .profile-photo {
            border-radius: 20px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(147, 51, 234, 0.3);
            transition: all 0.3s ease;
        }

        .profile-photo:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(147, 51, 234, 0.5);
        }

        .profile-info h3 {
            background: linear-gradient(to right, #E2E8F0, #94A3B8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn {
            position: relative;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .btn:hover::before {
            transform: translateX(100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.5);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: 15px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.2) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .profile {
            animation: float 6s ease-in-out infinite;
        }

        @media (max-width: 640px) {
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-actions {
                flex-direction: column;
                gap: 15px;
            }

            .btn-secondary {
                margin-left: 0;
            }

            .profile-photo-wrapper {
                margin: 0 auto;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-0">
    <div class="glow" id="glow"></div>
    
    <main class="container mx-auto my-10 max-w-3xl">
        <section class="profile glass-container p-8 sm:p-10">
            <h2 class="text-2xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-purple-600">
                Profil Bilgileri
            </h2>

            <div class="profile-info flex items-center space-x-8">
                <div class="profile-photo-wrapper">
                    <img src="<?php echo $user['profile_photo']; ?>" 
                         alt="Profil Fotoğrafı" 
                         class="profile-photo w-28 h-28 object-cover">
                </div>

                <div>
                    <h3 class="text-2xl font-bold mb-2"><?php echo $user['username']; ?></h3>
                    <p class="text-gray-400 text-lg"><?php echo $user['email']; ?></p>
                </div>
            </div>

            <div class="profile-actions mt-10 flex items-center">
                <a href="projects" class="btn btn-primary">Projelerim</a>
                <a href="settings" class="btn btn-secondary">Ayarlar</a>
            </div>
        </section>
    </main>

    <script>
        // Glow effect following mouse
        document.addEventListener('mousemove', (e) => {
            const glow = document.getElementById('glow');
            const x = e.clientX - 250;
            const y = e.clientY - 250;
            glow.style.left = `${x}px`;
            glow.style.top = `${y}px`;
        });

        // Add subtle hover effect to profile section
        const profileSection = document.querySelector('.profile');
        profileSection.addEventListener('mousemove', (e) => {
            const rect = profileSection.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 50;
            const rotateY = (centerX - x) / 50;
            
            profileSection.style.transform = `
                perspective(1000px)
                rotateX(${rotateX}deg)
                rotateY(${rotateY}deg)
                translateY(${Math.sin(Date.now() / 3000) * 10}px)
            `;
        });

        profileSection.addEventListener('mouseleave', () => {
            profileSection.style.transform = `
                translateY(${Math.sin(Date.now() / 3000) * 10}px)
            `;
        });
    </script>
</body>
</html>