<!DOCTYPE html>
<html>
<head>
    <title>Aduan Anda Telah Diterima</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px 0;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Aduan Anda Telah Diterima</h2>
    </div>
    
    <div class="content">
        <p>Kepada Yth. {{ $complaint->name }},</p>
        
        <p>Terima kasih atas aduan Anda. Kami telah menerima aduan Anda dengan detail sebagai berikut:</p>
        
        <div class="details">
            <p><strong>ID Aduan:</strong> {{ $complaint->id }}</p>
            <p><strong>Nama:</strong> {{ $complaint->name }}</p>
            <p><strong>Kategori:</strong> {{ $complaint->category }}</p>
            <p><strong>Deskripsi:</strong> {{ $complaint->description }}</p>
            <p><strong>Status:</strong> {{ ucfirst($complaint->status) }}</p>
        </div>
        
        <p>Aduan Anda akan segera diproses oleh tim kami. Kami akan menghubungi Anda kembali jika diperlukan informasi tambahan.</p>
        
        <p>Anda dapat memantau status aduan Anda menggunakan ID aduan di atas.</p>
    </div>
    
    <div class="footer">
        <p>Hormat kami,<br>Tim Warta Daerah Kabupaten Muna Barat</p>
        <p><small>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</small></p>
    </div>
</body>
</html>