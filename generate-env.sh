#!/bin/bash

# Get the current `UID`, `GID` and `USERNAME`.
APP_USER_ID=$(id -u)
APP_USER_GID=$(id -g)
APP_USER=$(whoami)

# Output to '.env' file.
echo ""
echo "Generating '.env' file..."
echo ""

cat <<EOL > .env
# Docker arguments.
APP_PORT=58080
APP_USER=${APP_USER}
APP_USER_ID=${APP_USER_ID}
APP_USER_GID=${APP_USER_GID}
PHPMYADMIN_PORT=58090
DB_NAME=db-technical-test
DB_USER=db-technical-test
DB_PSWD=db-technical-test
EOL

echo "'.env' file created with APP_USER_ID=${APP_USER_ID}, APP_USER_GID=${APP_USER_GID} and APP_USER=${APP_USER}"
echo ""

