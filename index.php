<?php
// VULNERABLE INTENTIONALLY — jangan dipakai di production
$db_password = "admin123";   // hardcoded secret

$conn = new mysqli("127.0.0.1", "root", "", "antrian");
if ($conn->connect_error) {
    die("DB error: " . $conn->connect_error);
}

$id = $_GET['id'] ?? '';

if ($id !== '') {
    // SQL Injection: input langsung di-concat ke query
    $result = $conn->query("SELECT * FROM tickets WHERE id = $id");

    // Reflected XSS: input di-echo tanpa escape
    echo "<h1>Detail Tiket: " . $_GET['id'] . "</h1>";

    if ($result && $row = $result->fetch_assoc()) {
        echo "<p>Nama: " . $row['nama'] . "</p>";
    }
} else {
    echo "<h1>Masukkan parameter ?id=1</h1>";
}
?>
<html>
  <body bgcolor="white">
    <table width="500">
      <tr><td>Form Tikett</td></tr>
    </table>
  </body>
</html>
