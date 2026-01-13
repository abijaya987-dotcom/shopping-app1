<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            padding-top: 20px;
        }
        
        .container {
            max-width: 800px;
        }
        
        .card {
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        
        .btn {
            border-radius: 5px;
        }
        
        .purchased {
            text-decoration: line-through;
            color: #888;
        }
        
        .food { border-left: 4px solid #28a745; }
        .drinks { border-left: 4px solid #17a2b8; }
        .toiletries { border-left: 4px solid #ffc107; }
        .cleaning { border-left: 4px solid #dc3545; }
        .others { border-left: 4px solid #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="text-primary">📝 Daftar Belanja</h3>
            <p class="text-muted">List Belanja Disini</p>
            <hr>
        </div>