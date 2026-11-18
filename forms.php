<?php
declare(strict_types=1);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
function respond(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function fail(int $status, string $message): void { respond($status, ['ok' => false, 'message' => $message]); }
$config = require __DIR__ . '/server-config.php';
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST'); fail(405, 'Method not allowed.');
}
if (!$config['enabled']) {
    respond(503, ['ready' => false, 'message' => 'Online submission is not available yet. Please email admin@thechorefacilitator.co.za.']);
}
$base = realpath($config['private_dir']);
$webroot = realpath($_SERVER['DOCUMENT_ROOT'] ?? __DIR__);
$site = realpath(__DIR__);
// Require an explicitly configured private directory. Never fall back to public_html.
if (!$base || !is_dir($base) || !is_writable($base) || !$webroot || !$site
    || $base === $webroot || strpos($base . DIRECTORY_SEPARATOR, $webroot . DIRECTORY_SEPARATOR) === 0
    || $base === $site || strpos($base . DIRECTORY_SEPARATOR, $site . DIRECTORY_SEPARATOR) === 0) {
    error_log('TCF forms: configure a writable private directory outside the website.');
    fail(503, 'Online submission is unavailable. Please email admin@thechorefacilitator.co.za.');
}
umask(0077);
foreach (['sessions', 'rate-limits'] as $directory) {
    $target = $base . DIRECTORY_SEPARATOR . $directory;
    if (!is_dir($target) && !@mkdir($target, 0700, true) && !is_dir($target)) {
        fail(503, 'Online submission is unavailable. Please email admin@thechorefacilitator.co.za.');
    }
    if (is_link($target) || !is_writable($target)) fail(503, 'Online submission is unavailable.');
}
$schema = json_decode(file_get_contents(__DIR__ . '/form-schema.json'), true);
$kind = $method === 'GET' ? ($_GET['form'] ?? '') : ($_POST['form_type'] ?? '');
if (!is_string($kind) || !isset($schema[$kind])) fail(400, 'Please select a valid enquiry form.');
if ($method === 'POST') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (!in_array($origin, $config['origins'], true)) fail(403, 'Please submit from The Chore Facilitator website.');
    if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 65536) fail(413, 'Please shorten your message and try again.');
}
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', '3600');
session_save_path($base . '/sessions');
session_name('tcf_form_session');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
if (!session_start()) fail(503, 'Online submission is unavailable.');
if ($method === 'GET') {
    if (empty($_SESSION['token']) || time() - ($_SESSION['issued'] ?? 0) > 3600) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        $_SESSION['issued'] = time();
    }
    $token = $_SESSION['token']; session_write_close();
    respond(200, ['ready' => true, 'token' => $token]);
}
$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || empty($_SESSION['token']) || time() - ($_SESSION['issued'] ?? 0) > 3600 || !hash_equals($_SESSION['token'], $token)) {
    fail(403, 'Your session expired. Please refresh and try again.');
}
if (!empty($_POST['website'])) fail(422, 'Unable to accept this submission.');
// Limit each source address to five submission attempts in fifteen minutes.
// Store only a hash and counter, never form content or the raw IP address.
$key = hash('sha256', __DIR__ . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
$limitFile = $base . '/rate-limits/' . $key . '.json';
$handle = @fopen($limitFile, 'c+');
if (!$handle || !flock($handle, LOCK_EX)) fail(503, 'Online submission is temporarily unavailable.');
$state = json_decode(stream_get_contents($handle) ?: '{}', true) ?: [];
if (time() - ($state['start'] ?? 0) >= 900) $state = ['start' => time(), 'count' => 0];
if (($state['count'] ?? 0) >= 5) {
    flock($handle, LOCK_UN); fclose($handle); header('Retry-After: 900');
    fail(429, 'Too many attempts. Please wait fifteen minutes or email admin@thechorefacilitator.co.za.');
}
$state['count']++; rewind($handle); ftruncate($handle, 0); fwrite($handle, json_encode($state)); fflush($handle); flock($handle, LOCK_UN); fclose($handle);
foreach (glob($base . '/rate-limits/*.json') ?: [] as $expired) {
    if (filemtime($expired) < time() - 86400) @unlink($expired);
}
$data = [];
foreach ($schema[$kind] as $field) {
    $array = substr($field['name'], -2) === '[]';
    $name = $array ? substr($field['name'], 0, -2) : $field['name'];
    $value = $_POST[$name] ?? ($array ? [] : '');
    if (($array && !is_array($value)) || (!$array && !is_string($value))) fail(422, 'Please check the form fields.');
    if ($array) {
        if (count($value) > 20) fail(422, 'Too many selections.');
        foreach ($value as $item) if (!is_string($item) || strlen($item) > 200) fail(422, 'Invalid selection.');
        $value = implode(', ', $value);
    }
    $value = trim(str_replace("\0", '', $value));
    if (($field['required'] && $value === '') || strlen($value) > ($field['type'] === 'textarea' ? 5000 : 500)) {
        fail(422, 'Please complete all required fields and keep messages within 5,000 characters.');
    }
    $data[$name] = $value;
}
$email = $data['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) fail(422, 'Please enter a valid email address.');
if (($data['privacy_consent'] ?? '') !== 'on') fail(422, 'Please confirm the privacy notice before submitting.');
if (!filter_var($config['recipient'], FILTER_VALIDATE_EMAIL) || !filter_var($config['sender'], FILTER_VALIDATE_EMAIL)) fail(503, 'Online submission is unavailable.');
$labels = ['candidate' => 'Candidate application', 'client' => 'Client placement brief', 'contact' => 'General enquiry'];
$body = "The Chore Facilitator — " . $labels[$kind] . "\n\n";
foreach ($data as $name => $value) $body .= ucwords(str_replace('_', ' ', $name)) . ":\n" . ($value ?: '(not supplied)') . "\n\n";
if (strlen($body) > 30000) fail(413, 'Please shorten your submission.');
$fingerprint = hash('sha256', $body);
if (($_SESSION['last_message'] ?? '') === $fingerprint && time() - ($_SESSION['last_sent'] ?? 0) < 120) fail(409, 'This submission was already accepted. Please allow time for a response.');
$headers = ['From' => 'The Chore Facilitator <' . $config['sender'] . '>', 'Reply-To' => $email, 'MIME-Version' => '1.0', 'Content-Type' => 'text/plain; charset=UTF-8'];
// This indicates host acceptance, not confirmed inbox delivery. Test delivery on xneelo.
$accepted = mail($config['recipient'], 'Website: ' . $labels[$kind], $body, $headers);
if (!$accepted) { error_log('TCF forms: mail transport rejected a submission.'); fail(503, 'Your message could not be sent. Your details are still here. Please try again or email admin@thechorefacilitator.co.za.'); }
$_SESSION['last_message'] = $fingerprint; $_SESSION['last_sent'] = time();
session_write_close();
respond(200, ['ok' => true]);
