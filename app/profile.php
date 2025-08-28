<?php
include 'auth.php';

class Profile
{
  public $username;
  public $isAdmin = false;
  function __toString()
  {
    return "User: " . htmlspecialchars($this->username, ENT_QUOTES, 'UTF-8') . ", Role: " . ($this->isAdmin ? "Admin" : "User");
  }
}

function safe_profile_decode($cookie)
{
  // Only allow unserialize if string matches expected format (JSON safer than PHP serialize)
  $data = json_decode($cookie, true);
  if (!is_array($data) || !isset($data['username']) || !isset($data['isAdmin']))
    return false;
  $p = new Profile();
  $p->username = preg_replace('/[^a-zA-Z0-9_.-]/', '', $data['username']);
  $p->isAdmin = !!$data['isAdmin'];
  return $p;
}

if (!isset($_COOKIE['profile'])) {
  die("Profile cookie tidak ditemukan. Silakan login ulang.");
}

$profile = safe_profile_decode($_COOKIE['profile']);
if (!$profile) {
  die("Profile data rusak atau tidak valid.");
}

$msg = '';
if ($profile->isAdmin && isset($_POST['delete_user'])) {
  $target = $_POST['delete_user'];
  // Validasi username
  if (!preg_match('/^[a-zA-Z0-9_.-]{1,32}$/', $target)) {
    $msg = "<p style='color:red'>Username tidak valid.</p>";
  } else {
    // Prepared statement untuk cegah SQL injection
    $stmt = $GLOBALS['PDO']->prepare("DELETE FROM users WHERE username = :username");
    $stmt->bindParam(':username', $target, PDO::PARAM_STR);
    $stmt->execute();
    $msg = "<p style='color:green'>User <b>" . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . "</b> berhasil dihapus!</p>";
  }
}

include '_header.php';
?>
<h2>Profile Page</h2>
<p><?php echo $profile; ?></p>

<?php if ($profile->isAdmin): ?>
  <h3>Admin Panel</h3>
  <form method="post" autocomplete="off">
    <label>Delete user:
      <select name="delete_user">
        <?php
        $users = $GLOBALS['PDO']->query("SELECT username FROM users");
        foreach ($users as $u) {
          if ($u['username'] !== $profile->username) {
            echo "<option value='" . htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') . "</option>";
          }
        }
        ?>
      </select>
    </label>
    <button type="submit">Delete</button>
  </form>
  <?php if (!empty($msg))
    echo $msg; ?>
<?php else: ?>
  <p style="color:red">You are a regular user. You do not have admin panel access.</p>
<?php endif; ?>

<?php include '_footer.php'; ?>