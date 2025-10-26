<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message - QuickChat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4927F5;
            margin-bottom: 10px;
        }
        .message-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4927F5;
            margin: 20px 0;
        }
        .sender-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f0edff;
            border-radius: 8px;
        }
        .avatar {
            display: table-cell;
            vertical-align: middle;
            width: 50px;
            height: 50px;
            padding-right: 15px;
        }
        .avatar-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #4927F5;
            color: white;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
            line-height: 50px;
        }
        .sender-details {
            display: table-cell;
            vertical-align: middle;
        }
        .sender-details h3 {
            margin: 0 0 4px 0;
            color: #333;
            font-size: 16px;
        }
        .sender-details p {
            margin: 2px 0;
            color: #666;
            font-size: 14px;
        }
        .message-content {
            font-size: 16px;
            line-height: 1.5;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .cta-button {
            display: inline-block;
            background-color: #4927F5;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .unsubscribe {
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }
        .unsubscribe a {
            color: #4927F5;
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">QuickChat</div>
            <p>Vous avez reçu un nouveau message !</p>
        </div>

        <div class="sender-info">
            <div class="avatar">
                <div class="avatar-circle">
                    {{ strtoupper(substr($senderName ?? 'U', 0, 1)) }}
                </div>
            </div>
            <div class="sender-details">
                <h3>{{ $senderName ?? 'Utilisateur' }}</h3>
                
                <p>{{ isset($message->created_at) ? $message->created_at->format('d/m/Y à H:i') : 'À l\'instant' }}</p>
            </div>
        </div>
        
        <div class="message-container">
            <div class="message-content">
                "{{ $messageContent ?? $message->content ?? '' }}"
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/chat" class="cta-button">
                💬 Répondre sur QuickChat
            </a>
        </div>

        <div class="footer">
            <p><strong>Bonjour {{ $recipient->name ?? 'cher utilisateur' }},</strong></p>
            <p>
                Vous recevez cet email car vous avez reçu un nouveau message sur QuickChat.
                Connectez-vous à votre compte pour voir tous vos messages et répondre à vos contacts.
            </p>

            <div class="unsubscribe">
                <p>
                    Vous ne souhaitez plus recevoir ces notifications ?
                    <a href="{{ config('app.url') }}/settings/notifications">Gérer mes notifications</a>
                </p>
                <p>© {{ date('Y') }} QuickChat - Tous droits réservés</p>
            </div>
        </div>
    </div>
</body>
</html>