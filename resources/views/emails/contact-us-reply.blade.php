<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1a1a2e;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .body {
            padding: 30px;
            color: #333333;
            line-height: 1.8;
        }
        .label {
            font-weight: bold;
            color: #1a1a2e;
        }
        .message-box {
            background-color: #f9f9f9;
            border-right: 4px solid #1a1a2e;
            padding: 15px 20px;
            border-radius: 4px;
            margin: 15px 0;
            color: #555;
        }
        .reply-box {
            background-color: #eef7ff;
            border-right: 4px solid #3b82f6;
            padding: 15px 20px;
            border-radius: 4px;
            margin: 15px 0;
            color: #1a1a2e;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>رد على رسالتك</h1>
        </div>
        <div class="body">
            <p>مرحباً <span class="label">{{ $contact->name }}</span>،</p>
            <p>شكراً لتواصلك معنا. فيما يلي رد فريقنا على رسالتك:</p>

            <p class="label">رسالتك الأصلية:</p>
            <div class="message-box">
                <p><span class="label">الموضوع:</span> {{ $contact->subject }}</p>
                <p>{{ $contact->message }}</p>
            </div>

            <p class="label">ردنا:</p>
            <div class="reply-box">
                {{ $contact->reply }}
            </div>

            <p>إذا كان لديك أي استفسار إضافي، لا تتردد في التواصل معنا مجدداً.</p>
            <p>مع تحياتنا،<br><span class="label">فريق الدعم</span></p>
        </div>
        <div class="footer">
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه مباشرة.</p>
        </div>
    </div>
</body>
</html>