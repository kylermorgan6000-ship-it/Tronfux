<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Credit Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }
        .credit-details {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .credit-details h3 {
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        .detail-value {
            color: #333;
            font-weight: 500;
        }
        .amount-highlight {
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
            text-align: center;
            padding: 15px 0;
        }
        .timestamp {
            font-size: 12px;
            color: #999;
            margin-top: 15px;
            text-align: right;
        }
        .info-text {
            font-size: 14px;
            color: #666;
            margin: 20px 0;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .warning-text {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>💰 Account Credit</h1>
            <p>Your {{ $accountType }} account has been credited</p>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hi <strong>{{ $user->name }}</strong>,</p>
            </div>

            <p class="info-text">
                Good news! Your {{ $accountType }} account has been credited by our admin team. The funds are now available in your account.
            </p>

            <div class="credit-details">
                <h3>Credit Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Credit Amount:</span>
                    <span class="detail-value">${{ $amount }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Account Type:</span>
                    <span class="detail-value">{{ $accountType }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">#{{ $transaction->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time:</span>
                    <span class="detail-value">{{ $transaction->created_at->format('M d, Y H:i A') }}</span>
                </div>
            </div>

            <div class="amount-highlight">
                +${{ $amount }}
            </div>

            <div class="warning-text">
                ⚠️ <strong>Note:</strong> If you didn't expect this credit or have any questions, please contact our support team immediately.
            </div>

            <div class="button-container">
                <a href="{{ config('app.url') }}" class="button">View Account</a>
            </div>

            <p class="info-text">
                If you have any questions or concerns regarding this transaction, please don't hesitate to contact our support team. We're here to help!
            </p>

            <p class="info-text">
                Best regards,<br>
                <strong>{{ config('app.name') }} Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>
                Questions? <a href="mailto:support@{{ config('app.url') }}">Contact Support</a> | 
                <a href="{{ config('app.url') }}/settings">Account Settings</a>
            </p>
            <p style="margin-top: 10px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
