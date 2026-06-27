<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$ca = __DIR__ . '/ca.pem';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_CA => $ca,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ]);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
    $stmt = $pdo->prepare('INSERT INTO prueba (nombre) VALUES (?)');
    $stmt->execute([$_POST['nombre']]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$filas = $pdo->query('SELECT * FROM prueba ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Prueba Aiven</title></head>
<body>
  <h1>Conexión correcta</h1>
  <form method="post">
    <input type="text" name="nombre" placeholder="Escribe un nombre" required>
    <button type="submit">Guardar</button>
  </form>
  <h2>Registros</h2>
  <ul>
    <?php foreach ($filas as $f): ?>
      <li><?= htmlspecialchars($f['id']) ?> - <?= htmlspecialchars($f['nombre']) ?> (<?= htmlspecialchars($f['creado_en'] ?? '') ?>)</li>
    <?php endforeach; ?>
  </ul>
</body>
</html>