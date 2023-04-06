service=wsspusher
docker stop $service
docker rm $service
docker build -t $service .

docker run -d --name $service -p 6006:6006 -p 678:80 --network=bridge $service

# docker exec $service php startserver.php start -d
