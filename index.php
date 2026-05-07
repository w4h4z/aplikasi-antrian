<?php
declare(strict_types=1);

// Ambil konfigurasi database dari environment variable
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'xecura';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'antrian';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    http_response_code(500);
    exit('Internal server error');
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$ticketName = null;
$message = 'Masukkan parameter ?id=1';

if ($id !== null && $id !== false) {
    try {
        $stmt = $conn->prepare('SELECT nama FROM tickets WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $ticketName = $row['nama'];
            $message = 'Detail Tiket: ' . $id;
        } else {
            $message = 'Tiket tidak ditemukan.';
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        error_log('Query error: ' . $e->getMessage());
        http_response_code(500);
        $message = 'Internal server error.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Tiket</title>
  <style>
    body {
      background-color: white;
      font-family: Arial, sans-serif;
    }

    table {
      width: 500px;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #cccccc;
      padding: 8px;
      text-align: left;
    }
  </style>
</head>
<body>
  <h1><?= escapeHtml($message) ?></h1>

  <?php if ($ticketName !== null): ?>
    <p>Nama: <?= escapeHtml($ticketName) ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th scope="col">Judul Form</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Form Tiket</td>
      </tr>
    </tbody>
  </table>
</body>
</html>
