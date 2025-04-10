<?php
session_start();
require 'connessione.php'; // Nota: ho corretto il nome del file da 'connection.php' a 'connessione.php' come nel login

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]); // Aggiunto campo last_name che mancava
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Controlla se l'email esiste già
    $checkEmail = $connessione->prepare("SELECT id FROM utenti WHERE email = :email");
    $checkEmail->bindParam(':email', $email);
    $checkEmail->execute();
    
    if ($checkEmail->rowCount() > 0) {
        $error = "L'email è già registrata!";
    } else {
        // Inserimento nel database
        $sql = "INSERT INTO utenti (first_name, last_name, email, password) 
                VALUES (:first_name, :last_name, :email, :password)";
        $stmt = $connessione->prepare($sql);
        $stmt->execute([
            ":first_name" => $first_name,
            ":last_name" => $last_name,
            ":email" => $email,
            ":password" => $password
        ]);

        // Redirect alla pagina di login
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="register-container">
        <h2>Registrati</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="POST">
            <label for="first_name">Nome</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Cognome</label>
            <input type="text" name="last_name" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            
            <div class="password-toggle">
                <input type="checkbox" onclick="togglePassword()"> Mostra Password
            </div>

            <button type="submit">Registrati</button>
        </form>
        <p>Hai già un account? <a href="login.php">Accedi</a></p>
    </div>

    <script>
        //funzione nascondi/mostra password
        function togglePassword() {
            var passField = document.getElementById("password");
            passField.type = (passField.type === "password") ? "text" : "password";
        }

        // Script per aggiungere particelle al background
document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.createElement('div');
    particlesContainer.classList.add('particles');
    document.body.appendChild(particlesContainer);
    
    // Crea 15 particelle
    for (let i = 0; i < 15; i++) {
        createParticle();
    }
    
    function createParticle() {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        // Posizione casuale
        const posX = Math.random() * 100;
        const delay = Math.random() * 15;
        const duration = 15 + Math.random() * 10;
        
        // Dimensione casuale
        const size = Math.floor(Math.random() * 20) + 5;
        
        // Stile personalizzato
        particle.style.left = `${posX}%`;
        particle.style.bottom = '-5%';
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.opacity = Math.random() * 0.5 + 0.1;
        
        particlesContainer.appendChild(particle);
        
        // Rimuovi e ricrea la particella dopo che l'animazione è finita
        setTimeout(() => {
            particle.remove();
            createParticle();
        }, (delay + duration) * 1000);
    }
});
    </script>
</body>
</html>