<?php
/**
 * 简易文件管理系统
 *
 * 允许用户上传和下载文件。
 *
 * 功能：
 * - 文件上传
 * - 文件下载
 * - 文件列表显示
 * - 以文本形式查看文件
 * - 删除文件
 * - 错误处理
 *
 */

// 设置上传目录
$uploadDir = './';
// 确保上传目录存在且可写
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die('无法创建上传目录');
    }
} elseif (!is_writable($uploadDir)) {
    die('上传目录不可写');
}

// 设置允许的文件类型和大小限制
$maxFileSize = 10 * 1024 * 1024; // 10MB

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // 检查是否有上传错误
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadError = match ($file['error']) {
            UPLOAD_ERR_INI_SIZE => '上传的文件超过了 php.ini 中 upload_max_filesize 选项设置的值。',
            UPLOAD_ERR_FORM_SIZE => '上传的文件超过了 HTML 表单中 MAX_FILE_SIZE 指令指定的值。',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传。',
            UPLOAD_ERR_NO_FILE => '没有文件被上传。',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹。',
            UPLOAD_ERR_CANT_WRITE => '文件写入磁盘失败。',
            UPLOAD_ERR_EXTENSION => '由于扩展停止了文件上传。',
            default => '未知上传错误',
        };
        echo '<script>alert("文件上传失败：' . $uploadError . '");</script>';
    } else {
        // 获取文件信息
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $filePath = $uploadDir . $fileName;

       if ($fileSize > $maxFileSize) {
            echo '<script>alert("文件大小超过限制。最大允许上传 ' . formatBytes($maxFileSize) . ' 的文件。");</script>';
        } else {
            // 移动上传的文件到目标目录
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                echo '<script>alert("文件上传成功！");</script>';
            } else {
                echo '<script>alert("文件上传失败，无法移动到目标目录。");</script>';
            }
        }
    }
}

// 处理文件下载
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);
    $filePath = $uploadDir . $fileName;

    // 安全检查：防止目录遍历
    if (strpos(realpath($filePath), realpath($uploadDir)) !== 0) {
        die('非法文件路径');
    }

    if (file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo '<script>alert("文件不存在！");</script>';
    }
}

// 处理文件查看
if (isset($_GET['view'])) {
    $fileName = basename($_GET['view']);
    $filePath = $uploadDir . $fileName;

    // 安全检查：防止目录遍历
    if (strpos(realpath($filePath), realpath($uploadDir)) !== 0) {
        die('非法文件路径');
    }

    if (file_exists($filePath)) {
        header('Content-Type: text/plain'); // 强制以纯文本显示
        readfile($filePath);
        exit;
    } else {
        echo '<script>alert("文件不存在！");</script>';
    }
}

// 处理文件删除
if (isset($_GET['delete'])) {
    $fileName = basename($_GET['delete']);
    $filePath = $uploadDir . $fileName;

    // 安全检查：防止目录遍历
    if (strpos(realpath($filePath), realpath($uploadDir)) !== 0) {
        die('非法文件路径');
    }

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo '<script>alert("文件删除成功！");</script>';
        } else {
            echo '<script>alert("文件删除失败！");</script>';
        }
    } else {
        echo '<script>alert("文件不存在！");</script>';
    }
}

// 格式化字节大小
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>简易文件管理系统</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans SC', sans-serif;
            background-color: #f3f4f6; /* 浅灰色背景 */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center; /* 水平居中 */
            min-height: 100vh; /* 确保内容至少占据整个视口的高度 */
            box-sizing: border-box; /* 包含 padding 和 border 在元素的总宽度和高度之内 */
        }
        h1 {
            color: #1f2937; /* 深灰色标题 */
            margin-top: 2rem; /* 增加顶部外边距 */
            margin-bottom: 1.5rem;
            text-align: center; /* 标题居中 */
        }
        form {
            background-color: #fff; /* 白色表单背景 */
            padding: 2rem;
            border-radius: 0.75rem; /* 圆角 */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* 阴影效果 */
            margin-bottom: 2rem; /* 增加底部外边距 */
            width: 90%; /* 限制表单宽度 */
            max-width: 400px; /* 设置表单最大宽度 */
            box-sizing: border-box;
        }

        form p {
            margin-bottom: 1rem;
            color: #374151;
        }
        input[type="file"] {
            margin-bottom: 1.5rem;
        }
        input[type="submit"] {
            background-color: #4caf50; /* 绿色按钮 */
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem; /* 圆角 */
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease; /* 平滑过渡效果 */
            width: 100%; /* 按钮宽度撑满容器 */
            font-size: 1rem;
            font-family: 'Noto Sans SC', sans-serif;
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* 鼠标悬停时颜色加深 */
        }
        #fileList {
            background-color: #fff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 90%;
            max-width: 800px; /* 限制文件列表最大宽度 */
            box-sizing: border-box;
            margin-bottom: 2rem;
        }

        #fileList h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #1f2937;
            text-align: center;
        }
        #fileList ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        #fileList li {
            border-bottom: 1px solid #e5e7eb; /* 分割线 */
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
            display: flex; /* 使用 Flexbox 布局 */
            justify-content: space-between; /* 两端对齐 */
            align-items: center; /* 垂直居中 */
        }
        #fileList li:last-child {
            border-bottom: none; /* 移除最后一个列表项的分割线 */
            padding-bottom: 0;
            margin-bottom: 0;
        }
        #fileList li a {
            color: #0078d7; /* 蓝色链接 */
            text-decoration: none;
            transition: color 0.2s ease; /* 平滑过渡效果 */
        }
        #fileList li a:hover {
            color: #0056b3; /* 鼠标悬停时颜色加深 */
            text-decoration: underline;
        }
        #fileList li span {
            color: #6b7280; /* 灰色文件大小 */
            font-size: 0.875rem;
            margin-left: 1rem; /* 与文件名之间增加间距 */
        }

        .no-files {
            color: #6b7280;
            text-align: center;
            margin-top: 1rem;
        }

        footer {
          margin-top: auto;
          text-align: center;
          padding: 1rem;
          font-size: 0.875rem;
          color: #9ca3af;
        }

        .file-actions {
            display: flex;
            gap: 0.5rem; /* 增加按钮之间的间距 */
        }
        .file-actions button, .file-actions a {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
            text-decoration: none; /* 移除默认的下划线 */
        }
        .file-actions button.delete {
            background-color: #dc3545; /* 红色 */
            color: white;
        }
        .file-actions button.delete:hover {
            background-color: #c82333; /* 颜色更深 */
        }
        .file-actions a.view {
            background-color: #0078d7; /* 蓝色 */
            color: white;
        }
        .file-actions a.view:hover {
            background-color: #0056b3; /* 颜色更深 */
        }

    </style>
</head>
<body>
    <h1>简易文件管理系统</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p>上传文件 (最大 <?php echo formatBytes($maxFileSize); ?>)：</p>
        <input type="file" name="file" required>
        <br>
        <input type="submit" value="上传">
    </form>

    <div id="fileList">
        <h2>文件列表</h2>
        <?php
            $files = scandir($uploadDir);
            $files = array_diff($files, ['.', '..']); // 移除 . 和 ..
            if (count($files) > 0):
        ?>
            <ul>
                <?php foreach ($files as $file): ?>
                    <li>
                        <a href="?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                        <span>(<?php echo formatBytes(filesize($uploadDir . $file)); ?>)</span>
                        <div class="file-actions">
                        <button onclick="window.location.href='?view=<?php echo urlencode($file); ?>'" class="view">以文本形式查看</button>
                            <button onclick="return confirm('确定要删除吗？')" class="delete" data-filename="<?php echo htmlspecialchars($file); ?>">删除</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="no-files">暂无文件。</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>© <?php echo date('Y'); ?> 简易文件管理系统. All rights reserved.</p>
    </footer>

    <script>
    // 使用事件委托来处理删除按钮点击事件
    document.getElementById('fileList').addEventListener('click', function(event) {
        if (event.target.classList.contains('delete')) {
            const fileName = event.target.dataset.filename;
            if (confirm('确定要删除文件 "' + fileName + '" 吗？')) {
                // 使用 fetch API 发送删除请求
                fetch('?delete=' + encodeURIComponent(fileName))
                    .then(response => {
                        if (response.ok) {
                            // 移除列表项
                            event.target.closest('li').remove();
                            alert('文件删除成功！');
                        } else {
                            alert('文件删除失败！');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('文件删除失败！');
                    });
            }
        }
    });
    </script>
</body>
</html>
