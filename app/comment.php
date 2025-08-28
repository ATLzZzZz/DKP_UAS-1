<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Post comments</h2>
<form method="post" autocomplete="off">
  <input name="author" placeholder="Name..." maxlength="32" required>
  <textarea name="content" placeholder="Comments..." maxlength="256" required></textarea>
  <button>Post</button>
</form>

<?php
$error = '';
if ($_POST) {
  $author = isset($_POST['author']) ? trim($_POST['author']) : '';
  $content = isset($_POST['content']) ? trim($_POST['content']) : '';
  if (!preg_match('/^[a-zA-Z0-9_. -]{1,32}$/', $author)) {
    $error = 'Nama tidak valid.';
  } elseif ($content === '') {
    $error = 'Komentar tidak boleh kosong.';
  } else {
    $stmt = $GLOBALS['PDO']->prepare("INSERT INTO comments(author,content,created_at) VALUES(?,?,datetime('now'))");
    $stmt->execute([$author, $content]);
  }
}
?>
<?php if ($error)
  echo '<p style="color:red">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</p>'; ?>
<h3>Comment listss : </h3>
<?php
foreach ($GLOBALS['PDO']->query("SELECT * FROM comments ORDER BY id DESC") as $row) {
  echo "<p><b>" . htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8') . "</b>: " . htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') . "</p>";
}
?>
<?php include '_footer.php'; ?>