<?php
$station_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['id'] ?? '');
$folder = 'stations/' . $station_id . '/';

$title = "Station nicht gefunden";
$desc = "";
$audio_file = "";
$images = [];

if (!empty($station_id) && is_dir($folder)) {
    // 1. Text auslesen
    if (file_exists($folder . 'info.txt')) {
        $lines = file($folder . 'info.txt', FILE_IGNORE_NEW_LINES);
        if (!empty($lines[0])) $title = htmlspecialchars($lines[0]);
        if (count($lines) > 1) {
            $desc = htmlspecialchars(implode("\n", array_slice($lines, 1)));
        }
    }

    // 2. Audio-Datei finden (.mp3)
    $audio_search = glob($folder . '*.mp3');
    if (!empty($audio_search)) {
        $audio_file = $audio_search[0];
    }

    // 3. Bilder aus dem Unterordner /images/ holen
    if (is_dir($folder . 'images/')) {
        $images = glob($folder . 'images/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <a href="index.php" class="back-link">&larr; Zurück zur Übersicht</a>
        <h1><?php echo $title; ?></h1>
    </header>

    <main class="container">
        <?php if (!empty($desc) || !empty($audio_file)): ?>
            <div class="audio-section">
                <?php if (!empty($audio_file)): ?>
                    <audio controls style="width: 100%; max-width: 500px; margin-bottom: 20px;">
                        <source src="<?php echo $audio_file; ?>" type="audio/mp3">
                        Ihr Browser unterstützt das Audio-Element nicht.
                    </audio>
                <?php endif; ?>
                <p style="text-align: left; font-size: 1.1rem; line-height: 1.7; white-space: pre-line;">
                    <?php echo $desc; ?>
                </p>
            </div>

            <?php if (!empty($images)): ?>
                <section>
                    <h2>Historische Aufnahmen</h2>
                    <div class="gallery">
                        <?php foreach ($images as $img): ?>
                            <img src="<?php echo $img; ?>" alt="Stationsbild">
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        <?php else: ?>
            <p>Diese Station existiert nicht oder ist leer.</p>
        <?php endif; ?>
    </main>
</body>
</html>
