<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Ping Server</h2>
<form method="get" autocomplete="off">
    <label for="target">Host/IP:</label>
    <input name="target" id="target" required pattern="^[a-zA-Z0-9_.:-]+$" maxlength="64"
        value="<?php echo isset($_GET['target']) ? htmlspecialchars($_GET['target'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    <button type="submit">Ping!</button>
</form>
<?php
$output = null;
$error = null;
if (isset($_GET['target'])) {
    $target = trim($_GET['target']);
    // Validate: only allow simple hostnames or IPv4/IPv6
    if (!preg_match('/^([a-zA-Z0-9_.:-]{1,64})$/', $target)) {
        $error = 'Host/IP tidak valid.';
    } else {
        // Prevent command injection
        $safeTarget = escapeshellarg($target);
        // Use -n for Windows, -c for Unix
        $pingCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "ping -n 2 $safeTarget" : "ping -c 2 $safeTarget";
        $output = shell_exec($pingCmd);
    }
}
if (isset($target)) {
    echo '<h3>Ping Result for: ' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '</h3>';
    if ($error) {
        echo '<div style="color:#b00">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    } elseif ($output !== null) {
        // Escape output for HTML
        echo '<pre>' . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . '</pre>';
    }
}
?>
<?php include '_footer.php'; ?>