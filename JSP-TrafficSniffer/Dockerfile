# 使用官方tomcat镜像作为基础镜像
FROM tomcat:9-jdk8

# 制作者信息
LABEL auther_template="CTF-Archives"

# 创建应用目录
RUN mkdir -p /usr/local/tomcat/webapps/ROOT

# 拷贝源码到ROOT目录
COPY ./src/jsp/* /usr/local/tomcat/webapps/ROOT/
COPY ./src/WEB-INF /usr/local/tomcat/webapps/ROOT/WEB-INF/

# 拷贝容器启动脚本
COPY ./service/docker-entrypoint.sh /

# 添加commons-fileupload依赖
RUN curl -L https://repo1.maven.org/maven2/commons-fileupload/commons-fileupload/1.4/commons-fileupload-1.4.jar -o /usr/local/tomcat/lib/commons-fileupload-1.4.jar \
    && curl -L https://repo1.maven.org/maven2/commons-io/commons-io/2.11.0/commons-io-2.11.0.jar -o /usr/local/tomcat/lib/commons-io-2.11.0.jar

# 创建上传目录并设置权限
RUN chmod 777 /usr/local/tomcat/webapps/*

# 设置服务
CMD ["/docker-entrypoint.sh"]