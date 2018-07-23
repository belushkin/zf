#Synopsis 
This is the application for traffic control for Kyiv users

#Installation
```bash
./toolbox.sh up
```

#Useful commands
```bash
./vendor/bin/doctrine-migrations generate
vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
```
###Tests
```bash
./toolbox.sh tests
```
###CLI mode
```bash
./toolbox.sh exec php console.php GET "/api/v1/getstops?lat=4711&lng=4567"
```
###APIDOC
```bash
./toolbox.sh apidoc
```

##Contributors @belushkin

##License MIT License
