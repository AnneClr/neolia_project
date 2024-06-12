<?php
$servername = "db";
$username = "dev_db_admin";
$password = "dev_secret_password";
$dbname = "dev_cyber_repository";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Récupérer tous les utilisateurs
$sqlQuery = "SELECT id, password FROM user WHERE password NOT LIKE '$2y$%'";
$result = $conn->query($sqlQuery);

while ($user = $result->fetch_assoc()) {
    // Hacher le mot de passe
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe dans la base de données
    $sqlUpdate = "UPDATE user SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param('si', $hashedPassword, $user['id']);
    $stmt->execute();
}