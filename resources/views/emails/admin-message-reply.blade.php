<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $replySubject }}</title>
</head>
<body style="margin:0;padding:0;background:#eef5fb;font-family:Arial,Helvetica,sans-serif;color:#10233b;">
    <div style="max-width:680px;margin:0 auto;padding:32px 20px;">
        <div style="background:linear-gradient(135deg,#0c2d4f,#1778cf);border-radius:24px;padding:28px 30px;color:#f7fbff;">
            <div style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;opacity:.75;margin-bottom:12px;">Planning and Evaluation Service</div>
            <h1 style="margin:0;font-size:32px;line-height:1.05;">Reply from DOST PES Portal</h1>
            <p style="margin:14px 0 0;font-size:15px;line-height:1.65;color:rgba(247,251,255,.82);">
                This is a follow-up to your message sent through the PES portal contact form.
            </p>
        </div>

        <div style="background:#ffffff;border-radius:22px;padding:28px 30px;margin-top:18px;box-shadow:0 18px 36px rgba(16,49,86,.08);">
            <div style="font-size:13px;letter-spacing:.12em;text-transform:uppercase;color:#1778cf;font-weight:700;margin-bottom:10px;">Subject</div>
            <h2 style="margin:0 0 18px;font-size:26px;line-height:1.2;color:#10233b;">{{ $replySubject }}</h2>

            <div style="font-size:16px;line-height:1.8;color:#243e5b;white-space:pre-line;">{{ $replyBody }}</div>

            <div style="margin-top:24px;padding-top:18px;border-top:1px solid rgba(16,49,86,.12);font-size:14px;line-height:1.7;color:#5b7188;">
                Original message sender: <strong style="color:#10233b;">{{ $contactMessage->name }}</strong><br>
                Original subject: <strong style="color:#10233b;">{{ $contactMessage->subject }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
