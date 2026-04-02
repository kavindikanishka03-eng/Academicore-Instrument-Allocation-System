# MySQL Password Reset Guide for Windows

## Stop MySQL Service
net stop MySQL80

## Find MySQL installation directory
# Common locations:
# C:\Program Files\MySQL\MySQL Server 8.0\
# C:\ProgramData\MySQL\MySQL Server 8.0\

## Create temporary init file
# Save this as C:\mysql-init.txt:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';

## Start MySQL with init file
# Navigate to MySQL bin directory, then run:
mysqld --init-file=C:\mysql-init.txt --console

## After MySQL starts successfully, stop it (Ctrl+C)

## Start MySQL service normally
net start MySQL80

## Test connection
mysql -u root -p
# Enter password: newpassword
