<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL 流量模拟</title>
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
        pre {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MySQL 流量模拟</h1>
        <?php
            // 数据库连接信息
            $servername = "127.0.0.1";
            $username = "root";
            $password = "123456";
            $dbname = "ctf";

            // 创建连接
            $conn = new mysqli($servername, $username, $password, $dbname);

            // 检查连接
            if ($conn->connect_error) {
                die("连接失败: " . $conn->connect_error);
            }

            // 执行查询
            error_reporting(0);
            $sql = "SELECT username, password FROM users WHERE id = " . $_GET["id"];
            $result = $conn->query($sql);

            // 显示查询和结果
            echo "<h5>执行的查询:</h5><pre>" . $sql . "</pre>";
            if ($result->num_rows > 0) {
                echo "<h5>查询结果:</h5><pre>";
                print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));
                echo "</pre>";
            } else {
                echo "<h5>查询结果:</h5><p>0 结果</p>";
            }

            // 关闭连接
            $conn->close();
        ?>
    </div>
</body>
</html>
