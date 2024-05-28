#!/usr/bin/env bash
#
# Script Name: 04-sed-vars.bash
# Author: Inge Gatovsky
# Date: 24/05/24

set -euo pipefail

DB_USER=$(cat /run/secrets/db_user)
DB_PASSWORD=$(cat /run/secrets/db_password)
MYSQL_ROOT_PASSWORD=$(cat /run/secrets/mysql_root_password)
DB_NAME=${DB_NAME:-}

echo "DB_USER: $DB_USER"
echo "DB_NAME: $DB_NAME"
echo "DB_PASSWORD: $DB_PASSWORD"
echo "MYSQL_ROOT_PASSWORD: $MYSQL_ROOT_PASSWORD"

# check if the variables are empty
if [ -z "$MYSQL_ROOT_PASSWORD" ] || [ -z "$DB_PASSWORD" ] || [ -z "$DB_NAME" ]; then
    echo "Usage: $0 MYSQL_ROOT_PASSWORD DB_PASSWORD DB_NAME"
    exit 1
fi


TEMP_SQL_FILE=$(mktemp)

# sed -e "s/\${DB_USER}/$DB_USER/g" \
#     -e "s/\${DB_PASSWORD}/$DB_PASSWORD/g" \
#     -e "s/\${DB_NAME}/$DB_NAME/g" \
#     /docker-entrypoint-initdb.d/03-set-user.sql > $TEMP_SQL_FILE

cat <<-EOSQL > $TEMP_SQL_FILE
USE ${DB_NAME};

-- Crate user if not exists
DELIMITER //
CREATE PROCEDURE create_user_if_not_exists()
BEGIN
  DECLARE user_exists INT DEFAULT 0;
  -- check if user exists
  SELECT COUNT(*) INTO user_exists FROM mysql.user WHERE user = '${DB_USER}' AND host = '%';
  IF user_exists = 0 THEN
    CREATE USER '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
  END IF;
  -- grant privileges
  GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%';
  FLUSH PRIVILEGES;
END //
DELIMITER ;

-- call the procedure
CALL create_user_if_not_exists();
DROP PROCEDURE IF EXISTS create_user_if_not_exists;
EOSQL

cat $TEMP_SQL_FILE

mysql -u root -p${MYSQL_ROOT_PASSWORD} < $TEMP_SQL_FILE

rm $TEMP_SQL_FILE

