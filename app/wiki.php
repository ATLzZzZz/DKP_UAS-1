<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Wiki Search</h2>
<form method="get" autocomplete="off">
    <input name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    <button>Search</button>
</form>
<?php
if (isset($_GET['q'])) {
    $q = $_GET['q'];
    // Escape for display, not for SQL
    $sql = "SELECT * FROM articles WHERE title LIKE :title";
    echo "<p>Query: " . htmlspecialchars($sql . " [title LIKE '%$q%']", ENT_QUOTES, 'UTF-8') . "</p>";
    // Use prepared statement for safety
    $stmt = $GLOBALS['PDO']->prepare($sql);
    $stmt->execute([':title' => "%$q%"]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<ul>';
    foreach ($res as $row) {
        echo "<li>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8') . "</li>";
    }
    echo '</ul>';
}
?>
<?php include '_footer.php'; ?>