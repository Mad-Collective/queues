#!/bin/bash

COMPONENT=${1}
COMPOSE_FILE="ops/docker/docker-compose.yml"
CONTAINERS=$(grep ':$' "${COMPOSE_FILE}" | grep -v '^  ' | cut -d ':' -f1 | tr '\n' ' ')

printf "
  Write the name of the service that you want to access, possible choices are:
  ${CONTAINERS}
  Service name: " && read -r SERVICE


docker exec -ti ${COMPONENT}_${SERVICE}_1 /bin/bash