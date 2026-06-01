<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gedenkseite KZ-Außenlager Barth</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Dokumentationsstelle KZ-Außenlager Barth</h1>
        <p>Ein digitaler Begleiter durch die Gedenkstätte</p>
    </header>

    <main class="container">
        <section>
            <h2>Audio-Guides & Stationen</h2>
            <div class="grid">
                <?php
                $dir = 'stations/';
                if (is_dir($dir)) {
                    // Alle Unterordner in /stations/ durchgehen
                    $folders = array_filter(glob($dir . '*'), 'is_dir');
                    
                    if (empty($folders)) {
                        echo "<p>Noch keine Stationen angelegt. Gehe zu <a href='admin.php'>admin.php</a>.</p>";
                    }

                    foreach ($folders as $folder) {
                        $station_id = basename($folder);
                        $title = str_replace('_', ' ', $station_id); // Fallback-Titel
                        $desc = "Keine Beschreibung verfügbar.";

                        // Falls info.txt existiert, Titel und Beschreibung auslesen
                        if (file_exists($folder . '/info.txt')) {
                            $lines = file($folder . '/info.txt', FILE_IGNORE_NEW_LINES);
                            if (!empty($lines[0])) $title = htmlspecialchars($lines[0]);
                            if (count($lines) > 1) {
                                $desc = htmlspecialchars(implode("\n", array_slice($lines, 1)));
                            }
                        }
                        ?>
                        <div class="card">
                            <h3><?php echo $title; ?></h3>
                            <p><?php echo substr($desc, 0, 120); ?>...</p>
                            <a href="guide.php?id=<?php echo $station_id; ?>" class="btn">Guide öffnen</a>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2026 Gedenkstätte Barth | <a href="admin.php">Admin-Bereich</a></p>
    </footer>
</body>
</html>
