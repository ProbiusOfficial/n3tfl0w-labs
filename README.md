## About

> hello-ctf.com 基础靶场计划，访问 [[hello-ctf.com 配套靶场]](https://hello-ctf.com/hc-labs/)  探索更多靶场。

**n3tfl0w-labs** 一个流量分析靶场。

和其他基础靶场一样，该靶场会从 0 到 1 带你入门流量分析这一方向。

流量分析考点参考：

```
# WEB流量分析：
HTTP流量分析 TLS流量分析
非连续型流量 - SQL盲注
AntSword / Godzilla / Behinder流量分析 
cs通信流量
# USB流量分析
键盘流量分析 鼠标流量分析 数位板流量分析 手柄流量分析 打印机流量分析
# 协议流量分析
FTP/FTP-DATA / SMTP / Telnet / MQTT 
/ ICMP(TTL、DATA.len、DATA、ICMP.code) /
TCP / UDP
# 场景类 - 工控协议
MMS / modbus / iec60870 /s7com / OMRON
# 其他 / 特殊
文件提取(dicom,ftp-data,http,imf,smb,tftp协议对象)
蓝牙
损坏流量恢复
其他非连续性流量
```

**核心工具：[Wireshark](https://www.wireshark.org/)** / Tshark



### 靶场模块

如你所见，靶场有多个文件夹，每个文件夹又有独立的readme，这是因为流量分析是一个很广很广的领域，列举所有的情况是不可能的，只能根据比赛还有实际情况添加一些常用场景。

可能会有一下内容：

基础流量演示 - 

Webshell流量分析 - 

常见流量(比如Webshell流量)的解密逻辑和对应脚本 - 

Tshark



### 辅助工具

一些可能会用到的流量分析相关软件：

**[abc123info - BlueTeamTools](https://github.com/abc123info/BlueTeamTools)** 

> 蓝队分析研判工具箱，可解密冰蝎流量、解密哥斯拉流量、解密Shiro/CAS/Log4j2的攻击payload

**[TrafficEye](https://github.com/CuriousLearnerDev/TrafficEye)**

> 蓝队网络流量分析,尤其针对 Web 应用的攻击（如 SQL 注入、XSS、WebShell 等）

**[PotatoTool](https://github.com/HotBoy-java/PotatoTool)**

> 网络安全综合工具
