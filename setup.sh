docker_compose_exec(){
  docker-compose exec wordpress su -s /bin/bash www-data -c "$1"
}

docker_compose_exec "wp core install --url=\"http://localhost:8080\" --title=\"Playground\" --admin_user=admin --admin_password=admin --admin_email=example@mail.com"
docker_compose_exec "wp plugin activate news"
