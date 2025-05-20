<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 自定义字体，如果需要的话 */
        body {
            font-family: 'Inter', 'Microsoft YaHei', sans-serif;
        }
        /* 确保在小屏幕上卡片内容不会溢出，并处理长单词 */
        .shell-card {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-all; /* 对于非截断的长字符串（如某些密码或密钥值）仍然有用 */
        }
        /* 复制按钮的最小宽度，防止文本变化时按钮大小跳动 */
        .copy-button {
            min-width: 100px; /* 根据实际文本调整 */
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-6 md:p-8">
    <?php
    // 获取当前URL的协议和主机名部分
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host;
    ?>
    <div class="container mx-auto max-w-6xl">
        <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8 sm:mb-10">WebShell</h1>


        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

            <div class="shell-card bg-white rounded-xl shadow-lg p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 mb-3">说明</h2>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">启用流量监听后使用Webshell管理工具连接shell，定向获取你想要的流量片段用于分析研究。</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-3 flex items-start">
                        <span class="font-medium text-gray-700 mr-1 whitespace-nowrap">示例命令:</span>
                        <span class="truncate" title="tcpdump -i any port 80 -w webshell.pcap" data-url="tcpdump -i any port 80 -w webshell.pcap">
                        tcpdump -i any port 80 -w webshell.pcap</span>
                    </div>
                </div>
                <button class="copy-url-btn mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition-colors duration-300 text-sm copy-button">
                    复制命令
                </button>
            </div>

            <div class="shell-card bg-white rounded-xl shadow-lg p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 mb-3">蚁剑 (AntSword)</h2>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">连接密码:</span>
                        <span class="ml-1">cmd</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-3 flex items-start">
                        <span class="font-medium text-gray-700 mr-1 whitespace-nowrap">URL:</span>
                        <span class="truncate" title="<?php echo htmlspecialchars($baseUrl . '/antsword.php'); ?>" data-url="<?php echo htmlspecialchars($baseUrl . '/antsword.php'); ?>">
                            <?php echo htmlspecialchars($baseUrl . '/antsword.php'); ?>
                        </span>
                    </div>
                </div>
                <button class="copy-url-btn mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition-colors duration-300 text-sm copy-button">
                    复制URL
                </button>
            </div>

            <div class="shell-card bg-white rounded-xl shadow-lg p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 mb-3">冰蝎 (Behinder)</h2>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">连接密码:</span>
                        <span class="ml-1">rebeyond</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-3 flex items-start">
                        <span class="font-medium text-gray-700 mr-1 whitespace-nowrap">URL:</span>
                        <span class="truncate" title="<?php echo htmlspecialchars($baseUrl . '/behinder.php'); ?>" data-url="<?php echo htmlspecialchars($baseUrl . '/behinder.php'); ?>">
                            <?php echo htmlspecialchars($baseUrl . '/behinder.php'); ?>
                        </span>
                    </div>
                </div>
                <button class="copy-url-btn mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition-colors duration-300 text-sm copy-button">
                    复制URL
                </button>
            </div>

            <div class="shell-card bg-white rounded-xl shadow-lg p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 mb-3">哥斯拉 (Godzilla) - BASE64</h2>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">密码:</span>
                        <span class="ml-1">pass</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">密钥:</span>
                        <span class="ml-1">key</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">加载器:</span>
                        <span class="ml-1">PHP_XOR_BASE64</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-3 flex items-start">
                        <span class="font-medium text-gray-700 mr-1 whitespace-nowrap">URL:</span>
                        <span class="truncate" title="<?php echo htmlspecialchars($baseUrl . '/godzilla_1.php'); ?>" data-url="<?php echo htmlspecialchars($baseUrl . '/godzilla_1.php'); ?>">
                            <?php echo htmlspecialchars($baseUrl . '/godzilla_1.php'); ?>
                        </span>
                    </div>
                </div>
                <button class="copy-url-btn mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition-colors duration-300 text-sm copy-button">
                    复制URL
                </button>
            </div>

            <div class="shell-card bg-white rounded-xl shadow-lg p-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                <div>
                    <h2 class="text-lg font-semibold text-blue-600 mb-3">哥斯拉 (Godzilla) - RAW</h2>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">密码:</span>
                        <span class="ml-1">pass</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">密钥:</span>
                        <span class="ml-1">key</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium text-gray-700">加载器:</span>
                        <span class="ml-1">PHP_XOR_RAW</span>
                    </div>
                    <div class="text-xs text-gray-600 mb-3 flex items-start">
                        <span class="font-medium text-gray-700 mr-1 whitespace-nowrap">URL:</span>
                        <span class="truncate" title="<?php echo htmlspecialchars($baseUrl . '/godzilla_2.php'); ?>" data-url="<?php echo htmlspecialchars($baseUrl . '/godzilla_2.php'); ?>">
                            <?php echo htmlspecialchars($baseUrl . '/godzilla_2.php'); ?>
                        </span>
                    </div>
                </div>
                <button class="copy-url-btn mt-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition-colors duration-300 text-sm copy-button">
                    复制URL
                </button>
            </div>
        </div>
    </div>

    <footer class="text-center text-gray-500 mt-12 pb-6">
        <p>&copy; <?php echo date("Y"); ?> Hello-CTF. All rights reserved.</p>
    </footer>

    <script>
        // 获取所有复制按钮
        const copyButtons = document.querySelectorAll('.copy-url-btn');

        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                // 找到与按钮关联的URL元素 (通过父级元素查找包含data-url的span)
                const urlSpan = this.parentElement.querySelector('span[data-url]');
                if (!urlSpan) {
                    console.error('URL span not found for this button.');
                    return;
                }
                const urlToCopy = urlSpan.dataset.url;

                if (!urlToCopy) {
                    console.error('URL to copy is empty or not found in data-url attribute.');
                    this.textContent = '复制失败';
                     setTimeout(() => {
                        this.textContent = '复制URL';
                    }, 2000);
                    return;
                }

                // 创建一个临时的textarea元素用于复制
                const textarea = document.createElement('textarea');
                textarea.value = urlToCopy;
                textarea.style.position = 'absolute'; // 防止页面滚动
                textarea.style.left = '-9999px';    // 移出屏幕外
                document.body.appendChild(textarea);
                
                textarea.select(); // 选择textarea中的文本
                textarea.setSelectionRange(0, 99999); // 兼容移动设备

                let success = false;
                try {
                    success = document.execCommand('copy'); // 执行复制命令
                } catch (err) {
                    console.error('Failed to copy URL using execCommand:', err);
                }
                
                document.body.removeChild(textarea); // 移除临时元素

                // 提供反馈
                if (success) {
                    const originalText = this.textContent;
                    this.textContent = '已复制!';
                    this.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                    this.classList.add('bg-green-500'); // 复制成功后按钮变绿色
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.classList.remove('bg-green-500');
                        this.classList.add('bg-blue-500', 'hover:bg-blue-600');
                    }, 2000); // 2秒后恢复
                } else {
                    this.textContent = '复制失败';
                     setTimeout(() => {
                        this.textContent = '复制URL';
                    }, 2000);
                }
            });
        });
    </script>

</body>
</html>