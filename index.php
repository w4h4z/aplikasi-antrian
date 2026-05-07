<?php
declare(strict_types=1);

// Ambil konfigurasi database dari environment variable
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_user = getenv('DB_USER') ?: 'xecura';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'antrian';

// Jangan tampilkan detail error database ke user
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    http_response_code(500);
    exit('Internal server error');
}

// Helper untuk output escaping
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Tiket</title>
</head>
<body>
<?php if ($id !== null && $id !== false): ?>

  <h1>Detail Tiket: <?= e((string) $id) ?></h1>

  <?php
  try {
      $stmt = $conn->prepare('SELECT nama FROM tickets WHERE id = ?');
      $stmt->bind_param('i', $id);
      $stmt->execute();

      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      if ($row) {
          echo '<p>Nama: ' . e($row['nama']) . '</p>';
      } else {
          echo '<p>Tiket tidak ditemukan.</p>';
      }

      $stmt->close();
  } catch (mysqli_sql_exception $e) {
      error_log('Query error: ' . $e->getMessage());
      http_response_code(500);
      echo '<p>Internal server error.</p>';
  }
  ?>

<?php else: ?>

  <h1>Masukkan parameter ?id=1</h1>

<?php endif; ?>

  <table width="500">
    <tr><td>Form Tiket</td></tr>
  </table>
</body>
</html>