#!/bin/bash

# 确保上传目录存在并具有正确的权限
mkdir -p /usr/local/tomcat/webapps/uploads
chmod 777 /usr/local/tomcat/webapps/*
# 运行tomcat服务
catalina.sh run

# sleep infinity