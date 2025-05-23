#!/bin/bash

service ssh start

sql_flag="flag{U_Ins3rt_&_I_c4tch_U}"

rm -f /docker-entrypoint.sh

mysqld_safe &

mysql_ready() {
	mysqladmin ping --socket=/run/mysqld/mysqld.sock --user=root --password=root > /dev/null 2>&1
}

while !(mysql_ready)
do
	echo "waiting for mysql ..."
	sleep 3
done


# 将FLAG写入文件 请根据需要修改
# echo $INSERT_FLAG | tee /home/$user/flag /flag

# 将FLAG写入数据库

if [[ -z $FLAG_COLUMN ]]; then
	FLAG_COLUMN="flag"
fi

if [[ -z $FLAG_TABLE ]]; then
	FLAG_TABLE="flag"
fi

mysql -u root -p123456 -e "
USE ctf;
create table $FLAG_TABLE (id varchar(300),data varchar(300));
insert into $FLAG_TABLE values('$FLAG_COLUMN','$sql_flag');
"


source /etc/apache2/envvars

echo "Running..." &

tail -F /var/log/apache2/* &

exec apache2 -D FOREGROUND