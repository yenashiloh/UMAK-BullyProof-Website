<!DOCTYPE html>
<html>
<head>
    <title>Account Disabled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Account Disabled Notification</h2>
        </div>
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            <p>We regret to inform you that your account has been disabled.</p>
            
            @if($reason)
            <p><strong>Reason:</strong> {{ $reason }}</p>
            @endif
            
            <p>If you believe this action was taken in error or if you have any questions, please contact CSFD for assistance.</p>
            
            <p>Thank you for your understanding.</p>
            
            <p>Best regards,<br>Center for Student Formation and Discipline</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>