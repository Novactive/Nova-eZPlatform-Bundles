# === Makefile Helper ===

# Variables
PHP_BIN := php
COMPOSER := composer
CURRENT_DIR := $(shell pwd)
EZ_DIR := $(CURRENT_DIR)/ezplatform

.PHONY: install
install: ## Install eZ as the local project
	@docker run -d -p 3339:3306 --name ezdbedithelpbundle -e MYSQL_ROOT_PASSWORD=ezplatform mariadb:10.2
	@composer create-project ezsystems/ezplatform --prefer-dist --no-progress --no-interaction --no-scripts $(EZ_DIR)
	@echo "Please set up this way:"
	@echo "\tenv(DATABASE_HOST)     -> 127.0.0.1"
	@echo "\tenv(DATABASE_PORT)     -> 3339"
	@echo "\tenv(DATABASE_PASSWORD) -> ezplatform"
	@cd $(EZ_DIR) && COMPOSER_MEMORY_LIMIT=-1 composer update --lock
    @cd $(EZ_DIR) && bin/console ezplatform:install clean
    @cd $(EZ_DIR) && bin/console cache:clear

.PHONY: serve
serve: stopez ## Clear the cache and start the web server
	@cd $(EZ_DIR) && rm -rf var/cache/*
	@docker start ezdbedithelpbundle
	@cd $(EZ_DIR) && bin/console cache:clear
	@cd $(EZ_DIR) && bin/console server:start

.PHONY: stopez
stopez: ## Stop the web server if it is running
	@if [ -f "$(EZ_DIR)/.web-server-pid" ] ; \
	then \
		 cd $(EZ_DIR) && php bin/console server:stop;  \
	fi;
	@docker stop ezdbedithelpbundle