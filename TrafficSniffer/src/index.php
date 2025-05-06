<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrafficSniffer 控制台</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #337ab7;
        }
        p {
            margin-bottom: 10px;
        }
        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        li {
            display: inline;
            margin: 0 15px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>TrafficSniffer 控制台</h1>
        <p style="text-align: center;">欢迎使用 TrafficSniffer！通过以下链接，您可以模拟不同类型的网络流量，并查看抓取结果。</p>
        <ul>
            <li><a href="shell.php" target="_blank">模拟 Webshell 交互</a></li>
            <li><a href="mysql.php" target="_blank">模拟 MySQL 交互</a></li>
            <li><a href="file.php" target="_blank">文件管理 & 抓取结果</a></li>
        </ul>
        <p style="text-align: center; font-size: small; color: #777;">当前服务器时间：<?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>