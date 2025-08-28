<?php
include 'auth.php';

class Profile
{
    public $username;
    public $isAdmin = false;

    function __construct($u, $isAdmin = false)
    {
        $this->username = $u;
        $this->isAdmin = $isAdmin;
    }

    function __toString()
    {
        return "User: {$this->username}, Role: " . ($this->isAdmin ? "Admin" : "User");
    }
}

if ($_POST) {
    $u = isset($_POST['username']) ? trim($_POST['username']) : '';
    $p = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi sederhana username dan password
    if (!preg_match('/^[a-zA-Z0-9_.-]{1,32}$/', $u)) {
        $error = "Username tidak valid.";
    } elseif ($p === '') {
        $error = "Password tidak boleh kosong.";
    } else {
        // Prepared statement untuk cegah SQL injection
        $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $u, PDO::PARAM_STR);
        $stmt->bindParam(':password', $p, PDO::PARAM_STR);
        $stmt->execute();
        if ($row = $stmt->fetch()) {
            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            $pObj = new Profile($row['username'], $row['role'] === 'admin');
            setcookie('profile', serialize($pObj));

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login failed.";
        }
    }
}
?>
<?php include '_header.php'; ?>
<h2>Login</h2>
<?php if (!empty($error))
    echo "<p style='color:red'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>"; ?>
<form method="post">
    <label>Username <input name="username"></label>
    <label>Password <input type="password" name="password"></label>
    <button type="submit">Login</button>
</form>
<?php include '_footer.php'; ?>