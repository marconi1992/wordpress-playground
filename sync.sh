docker_compose_exec(){
  docker-compose exec wordpress su -s /bin/bash www-data -c "$1"
}

docker_compose_exec "wp news sync"
