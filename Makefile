init:
	cp .env.example .env
	make up

up:
	docker-compose -f docker-compose.yml up -d

up-prod:
	docker-compose up -d

stop:
	docker-compose -f docker-compose.yml stop

restart:
	docker-compose -f docker-compose.yml restart

build:
	make down
	docker-compose -f docker-compose.yml up -d --build

down:
	docker-compose -f docker-compose.yml down

down-prod:
	docker-compose down

logs:
	docker-compose -f docker-compose.yml logs -f

bash:
	docker-compose exec firebird_app bash

composer-install:
	docker-compose -f docker-compose.yml exec app php composer.phar install

octane-reload:
	docker-compose -f docker-compose.yml exec app php artisan octane:reload

passport-install:
	docker-compose -f docker-compose.yml exec app php artisan passport:install

swagger-generate:
	docker-compose -f docker-compose.yml exec app php artisan l5-swagger:generate

test:
	docker-compose -f docker-compose.yml exec app php artisan test