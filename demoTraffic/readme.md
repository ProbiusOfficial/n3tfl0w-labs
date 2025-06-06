### About

一些基础演示流量：
蚁剑抓取根目录flag
冰蝎抓取根目录flag
哥斯拉抓取根目录flag
图片文件下载
压缩包下载
...

对于哥斯拉流量演示步骤对应：
START
测试连接
基本信息
命令执行： 
ls 
cat godzilla_?.php
文件管理：
下载 flag.png 文件
上传 file2upload 文件
数据库管理
连接 127.0.0.1 3306 root 12345 失败
连接 127.0.0.1 3306 root 123456 成功
SHOW TABLES FROM `ctf`
SELECT * FROM `ctf`.`flag` LIMIT 0,10
END