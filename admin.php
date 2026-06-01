<?php
session_start();

// ==========================================
// HIER DEIN PASSWORT EINTRAGEN
// ==========================================
$ADMIN_PASSWORD = "BarthGedenken2026"; 

if (isset($_POST['login'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Falsches Passwort!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$status = "";

if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    // Ordnername säubern (keine Sonderzeichen/Leerzeichen)
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['station_id']); 
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $base_dir = 'stations/';
    $station_dir = $base_dir . $id . '/';
    $images_dir = $station_dir . 'images/';

    // 1. Ordner-Struktur für die Station anlegen
    if (!is_dir($base_dir)) mkdir($base_dir, 0755);
    if (!is_dir($station_dir)) mkdir($station_dir, 0755);
    if (!is_dir($images_dir)) mkdir($images_dir, 0755);

    // 2. info.txt erzeugen (Zeile 1 = Titel, Zeile 2+ = Beschreibung)
    $info_content = $title . "\n" . $desc;
    file_put_contents($station_dir . 'info.txt', $info_content);

    // 3. Audio (.mp3) hochladen
    if (!empty($_FILES['audio_file']['tmp_name'])) {
        move_uploaded_file($_FILES['audio_file']['tmp_name'], $station_dir . 'audio.mp3');
    }

    // 4. Bilder in den Unterordner hochladen
    if (isset($_FILES['image_files'])) {
        foreach ($_FILES['image_files']['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) {
                $img_name = basename($_FILES['image_files']['name'][$key]);
                move_uploaded_file($tmp_name, $images_dir . $img_name);
            }
        }
    }

    $status = "<p style='color:green; font-weight:bold;'>Ordnerstruktur für '$id' erfolgreich erstellt!</p>";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Entwickler-Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <a href="index.php" class="back-link">&larr; Zurück zur Übersicht</a>
        <h1>Stations-Manager</h1>
    </header>
    <main class="container">
        <?php if (!$is_logged_in): ?>
            <div class="audio-section">
                <h2>Entwickler-Login</h2>
                <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <form method="POST">
                    <input type="password" name="password" placeholder="Passwort" required style="padding:10px; width:100%; max-width:300px; margin-bottom:15px;"><br>
                    <button type="submit" name="login" class="btn">Einloggen</button>
                </form>
            </div>
        <?php else: ?>
            <div class="audio-section">
                <h2>Neue Station generieren</h2>
                <a href="admin.php?logout=1" class="btn" style="background:#ef4444; margin-bottom:20px;">Abmelden</a>
                <?php echo $status; ?>

                <form method="POST" enctype="multipart/form-data" style="text-align:left; max-width:500px; margin:0 auto;">
                    <input type="hidden" name="upload" value="1">
                    
                    <label><b>Ordnername / ID</b> (z.B. <code>station1</code> - ohne Leerzeichen):</label>
                    <input type="text" name="station_id" required style="width:100%; padding:8px; margin-bottom:15px;">

                    <label><b>Titel der Station:</b></label>
                    <input type="text" name="title" required style="width:100%; padding:8px; margin-bottom:15px;">

                    <label><b>Beschreibungstext:</b></label>
                    <textarea name="description" rows="5" required style="width:100%; padding:8px; margin-bottom:15px;"></textarea>

                    <label><b>Audio-Datei (.mp3):</b></label><br>
                    <input type="file" name="audio_file" accept="audio/mp3" required style="margin-bottom:15px;"><br><br>

                    <label><b>Bilder für den Unterordner (Mehrfachauswahl):</b></label><br>
                    <input type="file" name="image_files[]" accept="image/*" multiple required style="margin-bottom:20px;"><br><br>

                    <button type="submit" class="btn" style="width:100%;">Ordnerstruktur anlegen</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
