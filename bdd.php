<?php
// Param�tres de connexion
$host = 'mysql-eem-hebergement.alwaysdata.net';
$dbname = 'eem-hebergement_bdd';   // Remplace par le nom de ta base de donn�es
$username = '409322'; // Remplace par ton nom d'utilisateur MySQL
$password = 'ethanF71500'; // Remplace par ton mot de passe MySQL

try {
    // Connexion � la base de donn�es avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // D�finir le mode d'erreur de PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message
    die("Erreur de connexion : " . $e->getMessage());
}
?>
