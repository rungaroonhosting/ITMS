<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request - ITMS</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(45deg, #1e3a8a, #f97316);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .content {
            padding: 30px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #1e3a8a, #f97316);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
            <p>IT Management System</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <p>Click the button below to reset your password:</p>
            
            <a href="{{ $resetUrl }}" class="btn">Reset Password</a>
            
            <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
            <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            
            <p>This password reset link will expire in 24 hours.</p>
            
            <p>If you did not request a password reset, no further action is required.</p>
            
            <p>Regards,<br>
            ITMS Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; 2025 IT Management System. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
