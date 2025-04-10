<?php
session_start();

define('GEMINI_API_KEY', 'AIzaSyDtaFmkF3c5aE-_ocO18F0UyQOqzQnpV1k');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent');

// Inizializza la cronologia delle chat se non esiste
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

function getGeminiResponse($domanda) {
    global $_SESSION;
    
    if (empty($domanda)) {
        return ["status" => "error", "message" => "La domanda non può essere vuota."];
    }

    // Costruisci la cronologia della conversazione per il contesto
    $conversationHistory = "";
    foreach ($_SESSION['chat_history'] as $entry) {
        $conversationHistory .= "Utente: " . $entry['domanda'] . "\n";
        $conversationHistory .= "Mentore: " . $entry['risposta'] . "\n\n";
    }

    // Prompt migliorato per il mentore virtuale con cronologia
    $prompt = "Sei un mentore di alto livello specializzato in startup, business e finanza. Aiuta giovani imprenditori a trasformare le loro idee in progetti concreti, offrendo consigli personalizzati, strategie pratiche e soluzioni innovative. Se la richiesta dell'utente non è inerente a startup, business o finanza, rispondi cortesemente che non disponi di informazioni pertinenti e invita l'utente a riformulare la domanda.
    
Cronologia della conversazione:
$conversationHistory

Nuova domanda dell'utente: $domanda";

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 800,
            "topP" => 0.95
        ]
    ];

    $ch = curl_init(GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["status" => "error", "message" => "Errore di connessione: " . $error];
    }

    curl_close($ch);
    $result = json_decode($response, true);

    if ($httpCode != 200) {
        $errorMsg = $result['error']['message'] ?? "Errore API (HTTP $httpCode)";
        return ["status" => "error", "message" => $errorMsg];
    }

    if (isset($result["candidates"][0]["content"]["parts"][0]["text"])) {
        return [
            "status" => "success",
            "message" => $result["candidates"][0]["content"]["parts"][0]["text"]
        ];
    }

    return ["status" => "error", "message" => "Risposta AI non valida."];
}

// Gestione della pulizia della chat
if (isset($_GET['clear_chat']) && $_GET['clear_chat'] == 1) {
    $_SESSION['chat_history'] = [];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents("php://input"), true);
    $domanda = trim($input["domanda"] ?? "");
    
    $response = getGeminiResponse($domanda);
    
    // Salva la domanda e la risposta nella cronologia se la risposta è valida
    if ($response["status"] === "success") {
        $_SESSION['chat_history'][] = [
            'domanda' => $domanda,
            'risposta' => $response["message"]
        ];
    }
    
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mentore Virtuale - Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Stili base della pagina */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #e0f7fa, #fce4ec);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 15px;
        }
        
        /* Container principale */
        .chat-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 800px;
            height: calc(100vh - 150px);
            margin: 0 auto;
        }
        
        /* Area della cronologia chat */
        .chat-history {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px 16px 0 0;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
            margin-bottom: 0;
        }
        
        /* Stili per i messaggi */
        .message {
            margin-bottom: 20px;
            clear: both;
            max-width: 85%;
        }
        
        .user-message {
            float: right;
            background: #007BFF;
            color: white;
            border-radius: 16px 16px 0 16px;
            padding: 12px 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .ai-message {
            float: left;
            background: white;
            color: #333;
            border-radius: 16px 16px 16px 0;
            padding: 12px 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            white-space: pre-wrap;
            line-height: 1.5;
        }
        
        /* Container del form */
        .input-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 10px;
        }
        
        #domanda {
            flex: 1;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
            resize: none;
            min-height: 25px;
            max-height: 100px;
            overflow-y: auto;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
        }
        
        button {
            background-color: #007BFF;
            color: white;
            padding: 0 20px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            height: 50px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        /* Pulsante pulisci chat */
        .clear-chat {
            margin-top: 15px;
            background-color: #e74c3c;
            padding: 10px 20px;
        }
        
        .clear-chat:hover {
            background-color: #c0392b;
        }
        
        /* Loader per indicare l'elaborazione */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007BFF;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
            display: inline-block;
            vertical-align: middle;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error {
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
            padding: 15px;
        }
        
        /* Assicura che i messaggi vengano visualizzati uno dopo l'altro */
        .message-container {
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            .chat-container {
                height: calc(100vh - 130px);
            }
            .message {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <h1>Il tuo Mentore Virtuale</h1>
    
    <div class="chat-container">
        <div class="chat-history" id="chat-history">
            <?php if (empty($_SESSION['chat_history'])): ?>
                <div style="text-align: center; color: #777; margin-top: 30px;">
                    Inizia la tua conversazione con il mentore virtuale!
                </div>
            <?php else: ?>
                <?php foreach ($_SESSION['chat_history'] as $entry): ?>
                    <div class="message-container">
                        <div class="message user-message">
                            <?php echo htmlspecialchars($entry['domanda']); ?>
                        </div>
                    </div>
                    <div class="message-container">
                        <div class="message ai-message">
                            <?php echo nl2br(preg_replace('/\*\*(.*?)\*\*/','<strong>$1</strong>', 
                                      preg_replace('/\*(.*?)\*/','<em>$1</em>', 
                                      htmlspecialchars($entry['risposta'])))); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="input-container">
            <textarea id="domanda" placeholder="Scrivi la tua domanda..." rows="1"></textarea>
            <button type="button" id="send-btn">Invia</button>
        </div>
    </div>
    
    <button class="clear-chat" id="clear-chat">Pulisci Chat</button>

    <script>
        const chatHistory = document.getElementById('chat-history');
        const domandaInput = document.getElementById('domanda');
        const sendBtn = document.getElementById('send-btn');
        const clearChatBtn = document.getElementById('clear-chat');
        
        // Funzione per scrollare automaticamente alla fine della chat
        function scrollToBottom() {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
        
        // Scroll iniziale
        scrollToBottom();
        
        // Invia messaggio su pressione Invio (senza Shift)
        domandaInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
            
            // Auto-espandi la textarea
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Invia messaggio con il pulsante
        sendBtn.addEventListener('click', sendMessage);
        
        // Pulisci chat
        clearChatBtn.addEventListener('click', function() {
            if (confirm('Sei sicuro di voler cancellare tutta la conversazione?')) {
                window.location.href = '?clear_chat=1';
            }
        });
        
        async function sendMessage() {
            const domanda = domandaInput.value.trim();
            if (!domanda) return;
            
            // Aggiungi il messaggio dell'utente alla chat
            const userMessageContainer = document.createElement('div');
            userMessageContainer.className = 'message-container';
            userMessageContainer.innerHTML = `<div class="message user-message">${escapeHtml(domanda)}</div>`;
            chatHistory.appendChild(userMessageContainer);
            
            // Crea un contenitore per la risposta dell'AI
            const aiMessageContainer = document.createElement('div');
            aiMessageContainer.className = 'message-container';
            const aiMessage = document.createElement('div');
            aiMessage.className = 'message ai-message';
            aiMessage.innerHTML = `<div class="loader"></div> Sto elaborando...`;
            aiMessageContainer.appendChild(aiMessage);
            chatHistory.appendChild(aiMessageContainer);
            
            // Pulisci l'input e fai lo scroll
            domandaInput.value = '';
            domandaInput.style.height = 'auto';
            scrollToBottom();
            
            try {
                const res = await fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domanda })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    // Formatta e visualizza la risposta
                    const formattedResponse = data.message
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/\n/g, '<br>');
                    
                    aiMessage.innerHTML = formattedResponse;
                } else {
                    aiMessage.innerHTML = `<span class="error">⚠️ ${data.message}</span>`;
                }
            } catch (err) {
                aiMessage.innerHTML = `<span class="error">⚠️ Errore: ${err.message}</span>`;
            }
            
            scrollToBottom();
        }
        
        // Funzione per escape HTML
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>