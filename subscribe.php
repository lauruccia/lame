<?php
// Raccolta email semplice: salva in data/iscrizioni.csv (cartella protetta da .htaccess).
// Opzionale: imposta NOTIFY_TO per ricevere una mail a ogni iscrizione.
const NOTIFY_TO = ''; // es. 'info@lamess.it'

header('Content-Type: application/json; charset=utf-8');
function out($ok, $err = null, $code = 200) {
    http_response_code($code);
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'error' => $err]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(false, 'Metodo non consentito.', 405);
if (!empty($_POST['sito'])) out(true); // honeypot: i bot fingono successo

$email = trim($_POST['email'] ?? '');
$nome  = trim(strip_tags($_POST['nome'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) out(false, 'Email non valida.', 422);
if (empty($_POST['consenso'])) out(false, 'Consenso necessario.', 422);

// Rate limit minimo per IP (1 invio ogni 10 secondi)
$dir = __DIR__ . '/data';
if (!is_dir($dir)) mkdir($dir, 0750, true);
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$rl = $dir . '/rl_' . md5($ip);
if (is_file($rl) && time() - filemtime($rl) < 10) out(false, 'Troppi tentativi, riprova tra poco.', 429);
touch($rl);

$file = $dir . '/iscrizioni.csv';
$new = !is_file($file);
$fh = fopen($file, 'a');
if (!$fh) out(false, 'Errore interno.', 500);
flock($fh, LOCK_EX);
if ($new) fputcsv($fh, ['data', 'email', 'nome', 'consenso']);
// protezione da CSV injection
$safe = fn($s) => preg_match('/^[=+\-@]/', $s) ? "'" . $s : $s;
fputcsv($fh, [date('c'), $safe($email), $safe($nome), 'si']);
flock($fh, LOCK_UN);
fclose($fh);

if (NOTIFY_TO) {
    @mail(NOTIFY_TO, 'Nuova iscrizione LAMÈSS', "Email: $email\nNome: $nome", "Content-Type: text/plain; charset=utf-8");
}
out(true);
