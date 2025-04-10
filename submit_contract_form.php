<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati inviati dal modulo
    $name = htmlspecialchars($_POST['name']);
    $surname = htmlspecialchars($_POST['surname']);
    $email = htmlspecialchars($_POST['email']);
    $province = htmlspecialchars($_POST['province']);
    $country = htmlspecialchars($_POST['country']);
    $message = htmlspecialchars($_POST['message']);

    // Impostazioni dell'email
    $to = "info@mentorship.it"; // Cambia con l'email a cui vuoi inviare il messaggio
    $subject = "Nuovo Messaggio di Contatto";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: $email" . "\r\n";
    
    // Corpo del messaggio
    $message_content = "
        <h2>Messaggio di contatto ricevuto</h2>
        <p><strong>Nome:</strong> $name $surname</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Provincia:</strong> $province</p>
        <p><strong>Paese:</strong> $country</p>
        <p><strong>Messaggio:</strong></p>
        <p>$message</p>
    ";

    // Invia l'email
    if (mail($to, $subject, $message_content, $headers)) {
        echo "<p>Il tuo messaggio è stato inviato con successo. Ti risponderemo al più presto.</p>";
    } else {
        echo "<p>Si è verificato un errore nell'invio del messaggio. Riprova più tardi.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaggio Inviato</title>
    <link rel="stylesheet" href="submit.css">
</head>
<body>
    <div class="container">
        <h1>Messaggio Inviato!</h1>
        <p>Grazie per averci contattato. Risponderemo al più presto possibile.</p>
        <a href="index.html" class="btn">Torna alla Home</a>
        <a href="contact.html" class="btn">Invia nuovo messaggio</a>
    </div>
</body>
</html>
