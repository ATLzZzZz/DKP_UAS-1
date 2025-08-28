<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Crash Test</h2>
<p>Uji pembagian aman. Masukkan penyebut (factor) &gt; 0.</p>
<?php
// Secure handling of user-supplied divisor to prevent division-by-zero and type juggling issues
$errors = [];
$result = null;

// Use filter_input for controlled retrieval
$rawFactor = filter_input(INPUT_GET, 'factor', FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
if ($rawFactor === null) {
    $factor = 1; // default when parameter absent
} else {
    // Normalize decimal comma to dot just in case
    $normalized = str_replace(',', '.', $rawFactor);
    if (!is_numeric($normalized)) {
        $errors[] = 'Parameter factor harus berupa angka.';
    } else {
        $factor = (float) $normalized;
        if ($factor == 0.0) {
            $errors[] = 'Factor tidak boleh 0 (division by zero).';
        } elseif ($factor < 0) {
            $errors[] = 'Factor harus bernilai positif.';
        } elseif ($factor > 1000000) {
            $errors[] = 'Factor terlalu besar.';
        }
    }
}

if (!$errors) {
    // Safe division
    $result = 100 / $factor;
}
?>

<form method="get" action="crash.php" style="margin-bottom:1em">
    <label for="factor">Factor:</label>
    <input type="number" step="any" min="0.0000001" name="factor" id="factor"
        value="<?php echo isset($factor) ? htmlspecialchars((string) $factor, ENT_QUOTES, 'UTF-8') : '1'; ?>" required>
    <button type="submit">Hitung</button>
</form>

<?php if ($errors): ?>
    <div style="color:#b00">
        <strong>Input error:</strong>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php elseif ($result !== null): ?>
    <p>100 / <?php echo htmlspecialchars((string) $factor, ENT_QUOTES, 'UTF-8'); ?> =
        <strong><?php echo htmlspecialchars((string) $result, ENT_QUOTES, 'UTF-8'); ?></strong></p>
<?php endif; ?>

<?php include '_footer.php'; ?>