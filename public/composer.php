<?php
$key = 'erp_secure_2026_X9kL';

if (!isset($_GET['key']) || $_GET['key'] !== $key) {
    http_response_code(403);
    die('Accesso negato.');
}

chdir(dirname(__DIR__));

// Trova PHP binary
$php = PHP_BINARY;

// Esegui composer install
$composerPath = __DIR__ . '/../vendor/bin/composer';
if (!file_exists($composerPath)) {
    // Prova percorso globale
    $composerPath = 'composer';
}

$output = shell_exec("$php $composerPath install --no-dev --optimize-autoloader --no-interaction 2>&1");

if ($output === null) {
    $output = shell_exec("composer install --no-dev --optimize-autoloader --no-interaction 2>&1");
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Composer Install — ERP Ortofrutta</title>
<style>
  body { font-family: monospace; background: #0f1117; color: #e2e8f0; margin: 0; padding: 40px; }
  h2 { font-family: sans-serif; font-size: 16px; font-weight: 600; color: #a3e635; margin: 0 0 20px; }
  pre { background: #1e2230; border: 1px solid #2d3748; border-radius: 8px; padding: 24px; font-size: 13px; line-height: 1.7; white-space: pre-wrap; word-break: break-word; }
  .ok   { color: #a3e635; }
  .warn { color: #facc15; }
  .err  { color: #f87171; }
  footer { margin-top: 20px; font-family: sans-serif; font-size: 12px; color: #4a5568; }
</style>
</head>
<body>

<h2>ERP Ortofrutta — composer install --no-dev --optimize-autoloader</h2>

<pre><?php
if ($output) {
    $lines = explode("\n", $output);
    foreach ($lines as $line) {
        $line = htmlspecialchars($line);
        if (stripos($line, 'installing') !== false || stripos($line, 'generating') !== false) {
            echo "<span class='ok'>{$line}</span>\n";
        } elseif (stripos($line, 'warning') !== false || stripos($line, 'nothing') !== false) {
            echo "<span class='warn'>{$line}</span>\n";
        } elseif (stripos($line, 'error') !== false || stripos($line, 'fail') !== false) {
            echo "<span class='err'>{$line}</span>\n";
        } else {
            echo $line . "\n";
        }
    }
} else {
    echo "<span class='err'>Nessun output. shell_exec potrebbe essere disabilitato sul server.</span>\n";
    echo "<span class='warn'>In tal caso carica vendor/ manualmente via FTP una volta sola.</span>";
}
?>
</pre>

<footer>
    Eseguito il <?php echo date('d/m/Y H:i:s'); ?> —
    <a href="https://erp.mrfruits.it" style="color:#4a5568">← Torna all'ERP</a>
</footer>

</body>
</html>