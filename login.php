<?php
session_start();
require 'connessione.php'; // Connessione al database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query 
    $stmt = $connessione->prepare("SELECT id, first_name, last_name, email, password FROM utenti WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente && password_verify($password, $utente['password'])) {
        $_SESSION['user_id'] = $utente['id'];
        $_SESSION['email'] = $utente['email'];
         // Imposta un cookie con il nome utente valido per 7 giorni
         setcookie("username", $utente['first_name'] . ' ' . $utente['last_name'], time() + (7 * 24 * 60 * 60), "/");
        header("Location: mentore_ai.php");
        exit();
    } else {
        $error = "Credenziali errate o utente non trovato.";
    }
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Collegamento al CSS -->
</head>
<body>
    <div class="login-container">
        <h2>Accedi</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            
            <div class="password-toggle">
                <input type="checkbox" onclick="togglePassword()"> Mostra Password
            </div>

            <button type="submit" name="login">Accedi</button>
        </form>
        <p>Non hai un account? <a href="register.php">Registrati</a></p>
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


