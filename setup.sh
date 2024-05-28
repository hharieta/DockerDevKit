#!/usr/bin/env bash

# Script Name: mange docker compose
# Author: Inge Gatovsky
# Date: 04/03/24
# Description: This script will manage docker-compose file 

set -euo pipefail
source colors.sh

DOCKER_BIN=$(which docker)
ACTION_UP=false
ACTION_DOWN=false
ACTION_REMOVE=false
TEMPLATE_PATH="./templates/"
TEMPLATE=""
DOCKERCOMPOSE_FILE=""
ABS_PATH=$(pwd)

usage() {
    cat << EOF
Usage: $0 [-u] [-d] [-r] [-T template] [-t type]

------- Listing options -------
  -u              Start the Docker Compose setup
  -d              Stop the Docker Compose setup
  -r              Remove the Docker Compose setup
  -T template     Specify the template directory (e.g., 'laravel' for templates/laravel/)
  -t type         Specify the type for matching a docker-compose file (e.g., 'apache' for docker-compose-apache.yaml)
EOF
    exit 1
}

# If no options were passed
if [[ $# -eq 0 ]]; then
    usage
fi

error_msg(){
    local msg=$1
    local exit_code=$?
    echo -e "${red}Error: ${msg} ${end}"
    echo -e "${yellow}Exiting...${exit_code} ${end}\n"
    exit $exit_code
}

success_msg(){
    local msg=$1
    echo -e "${green}Success: ${msg} ${end}\n"
}


function test_sock {
    if [[ -S /var/run/docker.sock ]]; then
        DOCKER_SOCK=/var/run/docker.sock
    elif [[ -S /run/user/$(id -u)/docker.sock ]]; then
        DOCKER_SOCK=/run/user/$(id -u)/docker.sock
    else
        # TODO: start docker service
        echo -e "\n${yellow}Docker Service dead or not exists${end}"
        echo -e "${yellow}Starting Docker...${end}"

        unameOut="$(uname -s)"
        if [[ $unameOut == "Linux" ]]; then
            sudo systemctl start docker
            sleep 10
        elif [[ $unameOut == "Darwin" ]]; then
            open /Applications/Docker.app
            sleep 10
        else
            error_msg "Unsupported OS"
        fi
        DOCKER_SOCK=/var/run/docker.sock
    fi
    success_msg "Docker Service is running"
}

test_sock

function check_scripts {
    local template=$1
    if [[ ! -d "${template}db/scripts" ]]; then
        error_msg "db/scripts directory not found"
    else
        # not use "" with "${template}db/scripts/*.sh"
        # to avoid interpreting like literal string
        for script in ${template}db/scripts/*.sh; do
            chmod +x $script || error_msg "Failed to change permission"
        done
    fi
}

function check_secrets {
    local template=$1
    REQUIRED_SECRETS=("mysql_root_password" "db_user" "db_password")
    for secret in "${REQUIRED_SECRETS[@]}"; do
        if [[ ! -f "${template}secrets/$secret" ]]; then
            error_msg "Secret file ${template}/secrets/$secret does not exist"
        fi
    done
}

function compose_up {
    local template=$1
    local docker_compose_file=$2
    if [[ $DOCKER_SOCK ]] && [[ -f "${template}${docker_compose_file}" ]]; then
        docker compose -f "${template}${docker_compose_file}" --env-file ${template}.env up -d --build --force-recreate --remove-orphans
    else
        if [[ ! -f "${template}${docker_compose_file}" ]]; then
            error_msg "docker-compose file not found"
        else
            error_msg "Docker Service not running"
        fi
    fi
}

function compose_down {
    local template=$1
    local docker_compose_file=$2
    if [[ $DOCKER_SOCK ]] && [[ -f "${template}${docker_compose_file}" ]]; then
        docker compose -f "${template}${docker_compose_file}" down -v
    else
        if [[ ! -f "${template}${docker_compose_file}" ]]; then
            error_msg "docker-compose file not found"
        else
            error_msg "Docker Service not running"
        fi
    fi

}

### MAIN FUNCTION ###
while getopts ":udrT:t:" opt; do
    case $opt in
        u)
            ACTION_UP=true
            ;;
        d)
            ACTION_DOWN=true
            ;;
        r)
            ACTION_REMOVE=true
            ;;
        T)
            TEMPLATE="${TEMPLATE_PATH}${OPTARG}/"
            ;;
        t)
            DOCKERCOMPOSE_FILE="docker-compose-${OPTARG}.yaml"
            ;;
        \?)
            echo -e "${red}Invalid option${end}" >&2
            usage
            ;;
        :)
            echo -e "${red}Option -$OPTARG requires an argument.${end}"
            usage
    esac
done


if [[ -z "${TEMPLATE}${DOCKERCOMPOSE_FILE}" ]]; then
    usage
fi

if [[ "$ACTION_UP" == true ]]; then
    check_secrets "${TEMPLATE}"
    check_scripts "${TEMPLATE}"
    compose_up "${TEMPLATE}" "${DOCKERCOMPOSE_FILE}"
elif [[ "$ACTION_DOWN" == true ]]; then
    compose_down "${TEMPLATE}" "${DOCKERCOMPOSE_FILE}"
elif [[ "$ACTION_REMOVE" == true ]]; then
    echo -e "${TEMPLATE}db/data"
    rm -rf ${TEMPLATE}db/data/* || error_msg "Failed to remove data from ${TEMPLATE}/db/data/*"
else
    usage
fi

