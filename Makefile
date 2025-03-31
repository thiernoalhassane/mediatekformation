server := "gehu1211@epinoche.o2switch.net"
domain := "mediatekformation"

.PHONY: install deploy

deploy:
	ssh gehu1211@epinoche.o2switch.net 'cd www/mediatekformation && git pull origin main && make install'

install: vendor/autoload.php   
   php bin/console cache:clear

vendor/autoload.php: composer.lock composer.json
	composer install --no-dev --optimize-autoloader