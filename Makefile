# === Makefile Helper ===

# Styles
YELLOW=$(shell echo "\033[00;33m")
RED=$(shell echo "\033[00;31m")
RESTORE=$(shell echo "\033[0m")


# Variables
UNAME_S := $(shell uname -s)
PHP_BIN := php
COMPOSER := composer
CURRENT_DIR := $(shell pwd)
SYMFONY := symfony
EZ_DIR := $(CURRENT_DIR)/ezplatform
CHROMEDRIVER := $(CURRENT_DIR)/chromedriver
DOCKER := docker
DOCKER_DB_CONTAINER := dbezplbundl
MYSQL := mysql -f -u root -pezplatform -h 127.0.0.1 -P 3300 ezplatform
CHROME_DRIVER_URL := https://chromedriver.storage.googleapis.com/84.0.4147.30/chromedriver_linux64.zip
ifeq ($(UNAME_S),Darwin)
CHROME_DRIVER_URL := https://chromedriver.storage.googleapis.com/84.0.4147.30/chromedriver_mac64.zip
endif

.DEFAULT_GOAL := list

.PHONY: list
list:
	@echo "******************************"
	@echo "${YELLOW}Available targets${RESTORE}:"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf " ${YELLOW}%-15s${RESTORE} > %s\n", $$1, $$2}'
	@echo "${RED}==============================${RESTORE}"

.PHONY: codeclean
codeclean: ## Coding Standard checks
	$(PHP_BIN) ./vendor/bin/php-cs-fixer fix --config=.cs/.php_cs.php
	$(PHP_BIN) ./vendor/bin/phpcs --standard=.cs/cs_ruleset.xml --extensions=php src/ components/ bin/

.PHONY: codecheckmess
codecheckmess: codeclean ## Mess checking
	$(PHP_BIN) ./vendor/bin/phpmd src,components,bin text .cs/md_ruleset.xml # not enable yet, needs to review all

.PHONY: install
install: ## Install vendors
	@$(COMPOSER) install
	@wget -O chromedriver.zip "$(CHROME_DRIVER_URL)" && unzip -o chromedriver.zip && rm chromedriver.zip


.PHONY: installez
installez: install ## Install eZ as the local project
	@$(DOCKER) run -d -p 3300:3306 --name $(DOCKER_DB_CONTAINER) -e MYSQL_ROOT_PASSWORD=ezplatform mariadb:10.3
	@$(COMPOSER) create-project ezsystems/ezplatform --prefer-dist --no-progress --no-interaction --no-scripts $(EZ_DIR)
	@echo "DATABASE_URL=mysql://root:ezplatform@127.0.0.1:3300/ezplatform" >>  $(EZ_DIR)/.env.local
	@cd $(EZ_DIR) && $(COMPOSER) ezplatform-install
	@cd $(EZ_DIR) && bin/console cache:clear
	@for COMPONENT in $(shell ls components); do \
		if COMPONENT=$${COMPONENT} bin/ci-should install; then \
    		echo " ..:: Installing $${COMPONENT} ::.."; \
			COMPONENT_CONFIG_DIR="components/$${COMPONENT}/tests/provisioning" COMPONENT=$${COMPONENT} bin/wrapbundle; \
		fi \
	done
	@cd $(EZ_DIR) && $(COMPOSER) update --lock
	@cd $(EZ_DIR) && bin/console cache:clear

	@echo "..:: Do bundle specifics ::.."
	@$(MYSQL) < components/SEOBundle/bundle/Resources/sql/schema.sql

	@echo "..:: Final Cleaning Cache ::.."
	@cd $(EZ_DIR) && bin/console cache:clear

.PHONY: serveez
serveez: stopez ## Clear the cache and start the web server
	@cd $(EZ_DIR) && rm -rf var/cache/*
	@$(DOCKER) start $(DOCKER_DB_CONTAINER)
	@cd $(EZ_DIR) && bin/console cache:clear
	@cd $(EZ_DIR) && $(SYMFONY) local:server:start -d --port=11083
	@cd $(EZ_DIR) && $(SYMFONY) run -d --watch=ezplatform/config,ezplatform/src,ezplatform/vendor,components symfony console messenger:consume ezaccelerator


.PHONY: stopez
stopez: ## Stop the web server if it is running
	@-cd $(EZ_DIR) && $(SYMFONY) local:server:stop
	@-$(DOCKER) stop $(DOCKER_DB_CONTAINER)


.PHONY: tests
tests: serveez ## Run the tests
	@for COMPONENT in $(shell ls components); do \
    	if COMPONENT=$${COMPONENT} bin/ci-should test; then \
    		echo " ..:: Testing $${COMPONENT} ::.."; \
    		PANTHER_EXTERNAL_BASE_URI="https://127.0.0.1:11083" PANTHER_CHROME_DRIVER_BINARY=$(CHROMEDRIVER) $(PHP_BIN) ./vendor/bin/phpunit "components/$${COMPONENT}/tests" --exclude-group behat; \
		fi \
	done

.PHONY: ps
ps: ## Show docker-compose services
	@cd $(EZ_DIR) && $(SYMFONY) server:status
	@echo "\n!!!${RED}careful${RESTORE}!!!, if you change files outside the watched folders, you need to ${YELLOW}kill $PID${RESTORE} and re-rerun ${YELLOW}make consume${RESTORE}."


.PHONY: clean
clean: stopez ## Removes the vendors, and caches
	@-rm -f .php_cs.cache
	@-rm -rf vendor
	@-rm -f chromedriver
	@-rm -rf ezplatform
	@-$(DOCKER) rm $(DOCKER_DB_CONTAINER)