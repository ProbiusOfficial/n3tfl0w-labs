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



### 开源脚本

[5ime / CS_Decrypt](https://github.com/5ime/CS_Decrypt)

> CobaltStrike流量解密脚本

[melody27 / behinder_decrypt](https://github.com/melody27/behinder_decrypt)

> 冰蝎流量解密脚本

[godzilla_decode](https://github.com/AlphabugX/godzilla_decode)

> 哥斯拉jsp(内存马)流量解密

[Deco_Godzilla](https://github.com/nocultrue/Deco_Godzilla)

> 解密哥斯拉所有类型流量

[kingkong](https://github.com/H4ckForJob/kingkong)

> 哥斯拉jsp类型的webshell流量解密

[godzilla_decoder](https://github.com/think3t/godzilla_decoder)

> **[哥斯拉Godzilla](https://github.com/BeichenDream/Godzilla)** 加密流量分析的辅助脚本

[P001water / UsbKbCracker](https://github.com/P001water/UsbKbCracker)

> CTF中常见键盘流量解密脚本

[Mumuzi7179 / UsbKeyboard_Mouse_Hacker_Gui](https://github.com/Mumuzi7179/UsbKeyboard_Mouse_Hacker_Gui)

> 自带GUI的一键解鼠标流量/键盘流量小工具

[WangYihang / UsbKeyboardDataHacker](https://github.com/WangYihang/UsbKeyboardDataHacker)

> USB键盘流量包取证工具 , 用于恢复用户的击键信息

### 辅助工具

一些可能会用到的流量分析相关软件：

**[abc123info - BlueTeamTools](https://github.com/abc123info/BlueTeamTools)** 

> 蓝队分析研判工具箱，可解密冰蝎流量、解密哥斯拉流量、解密Shiro/CAS/Log4j2的攻击payload

**[TrafficEye](https://github.com/CuriousLearnerDev/TrafficEye)**

> 蓝队网络流量分析,尤其针对 Web 应用的攻击（如 SQL 注入、XSS、WebShell 等）

**[PotatoTool](https://github.com/HotBoy-java/PotatoTool)**

> 网络安全综合工具

### Webshell相关

#### webshell

**中国菜刀** - https://github.com/raddyfiy/caidao-official-version

**蚁剑流量分析**  - [releases-2.1.15](https://github.com/AntSwordProject/antSword/releases/tag/2.1.15) 

**哥斯拉流量** - [v4.0.1-godzilla](https://github.com/BeichenDream/Godzilla/releases/tag/v4.0.1-godzilla)

**冰蝎**

主要以最新Release为主，其他版本流量特征会有变更，有兴趣可依靠靶场环境自行研究。

[Behinder_v4.1【t00ls专版】](https://github.com/rebeyond/Behinder/releases/tag/Behinder_v4.1%E3%80%90t00ls%E4%B8%93%E7%89%88%E3%80%91)

反编译源码：[MountCloud/BehinderClientSource](https://github.com/MountCloud/BehinderClientSource)  

>  其他发行版本：[Behinder_v3.0.11【t00ls专版】](https://github.com/rebeyond/Behinder/releases/tag/Behinder_v3.0_Beta_11_for_tools) | [冰蝎 v2.0.1](https://github.com/rebeyond/Behinder/releases/tag/Behinder_v2.0.1) | [冰蝎v1.2.1](https://github.com/rebeyond/Behinder/releases/tag/Behinder_v1.2.1)

#### 分析文章

[【freebuf - 2021-08-22 - 哥斯拉Godzilla加密流量分析<3.03>】](https://www.freebuf.com/sectool/285693.html)

### Q & A

**历史上的流量分析赛题很多了 为什么要有这个项目？**

：事实证明拿历史赛题来给新手讲流量分析是不可行的 

“老师为什么他下载下来的文件还要解这么多层加密啊，webshell这么厉害么”

 “傻孩子，这不是webshell干的，这他妈是出题人的脑花!🧠”
