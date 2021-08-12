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
DOCKER := docker
DOCKER_DB_CONTAINER := dbezplbundl
MYSQL := mysql -f -u root -pezplatform -h 127.0.0.1 -P 3300 ezplatform
CONSOLE := $(PHP_BIN) bin/console

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
	$(PHP_BIN) ./vendor/bin/phpmd src,components,bin text .cs/md_ruleset.xml

.PHONY: install
install: ## Install vendors
	@$(COMPOSER) install
	@$(PHP) vendor/bin/bdi detect drivers

.PHONY: post-install
post-install:
	@echo "..:: Do bundle YARN deps ::.."
	@ln -sf $(EZ_DIR)/node_modules
	@cd $(EZ_DIR) && yarn add --dev algoliasearch react react-collapsible react-dom react-instantsearch-dom

	@echo "..:: Put Bundles in there ::.."
	@for COMPONENT in $(shell ls components); do \
		if COMPONENT=$${COMPONENT} bin/ci-should install; then \
    		echo " ..:: Installing $${COMPONENT} ::.."; \
			COMPONENT_CONFIG_DIR="components/$${COMPONENT}/tests/provisioning" COMPONENT=$${COMPONENT} bin/wrapbundle; \
		fi \
	done
	@cd $(EZ_DIR) && $(COMPOSER) update
	@cd $(EZ_DIR) && $(CONSOLE) cache:clear

	@echo "..:: Do bundle specifics ::.."
	@$(MYSQL) < components/SEOBundle/bundle/Resources/sql/schema.sql
	@cd $(EZ_DIR) && $(CONSOLE) novaezextra:contenttypes:create ../tests/vmcd.xlsx
	@cd $(EZ_DIR) && $(CONSOLE) novaezprotectedcontent:install
	@cd $(EZ_DIR) && $(CONSOLE) novaezhelptooltip:create
	@cd $(EZ_DIR) && $(CONSOLE) doctrine:schema:update --dump-sql --force
	@cd $(EZ_DIR) && $(CONSOLE) novaezmailing:install
	@cp -rp components/ProtectedContentBundle/tests/provisioning/article.html.twig $(EZ_DIR)/templates/themes/standard/full/
	@cp -rp components/StaticTemplatesBundle/tests/provisioning/static_ultimatenova $(EZ_DIR)/templates/themes/

	@echo "..:: Final Cleaning Cache ::.."
	@cd $(EZ_DIR) && $(CONSOLE) cache:clear

.PHONY: installez
installez: install ## Install eZ as the local project
	@$(DOCKER) run -d -p 3300:3306 --name $(DOCKER_DB_CONTAINER) -e MYSQL_ROOT_PASSWORD=ezplatform mariadb:10.3
	@$(COMPOSER) create-project ezsystems/ezplatform --prefer-dist --no-progress --no-interaction --no-scripts $(EZ_DIR)

	@echo "..:: Do eZ Install ::.."
	@echo "DATABASE_URL=mysql://root:ezplatform@127.0.0.1:3300/ezplatform" >>  $(EZ_DIR)/.env.local
	@cd $(EZ_DIR) && $(COMPOSER) ezplatform-install
	@cd $(EZ_DIR) && $(CONSOLE) cache:clear

	@(MAKE) post-install


.PHONY: installibexa
installibexa: install ## Install Ibexa as the local project
	@$(DOCKER) run -d -p 3300:3306 --name $(DOCKER_DB_CONTAINER) -e MYSQL_ROOT_PASSWORD=ezplatform mariadb:10.4
	@$(COMPOSER) create-project ibexa/website-skeleton --prefer-dist --no-progress --no-interaction $(EZ_DIR)
	@cd $(EZ_DIR) && $(COMPOSER) require ibexa/oss --no-interaction
	@echo "..:: Do Ibexa Install ::.."
	@echo "DATABASE_URL=mysql://root:ezplatform@127.0.0.1:3300/ezplatform" >>  $(EZ_DIR)/.env.local
	@cd $(EZ_DIR) && git init && git add . &&  git commit -m "Fake commit that will be removed"
	@cd $(EZ_DIR) && $(COMPOSER) recipes:install ibexa/oss --force
	@rm -rf $(EZ_DIR)/.git

	@cd $(EZ_DIR) && $(CONSOLE) ibexa:install
	@cd $(EZ_DIR) && $(CONSOLE) ibexa:graphql:generate-schema
	@cd $(EZ_DIR) && $(COMPOSER) run post-update-cmd
	@$(MAKE) post-install
	@cd $(EZ_DIR) && $(COMPOSER) update
	@cd $(EZ_DIR) && $(COMPOSER) require -W phpunit/phpunit:^9.5 symfony/phpunit-bridge:^5.3


.PHONY: serve
serve: stop ## Clear the cache and start the web server
	@cd $(EZ_DIR) && rm -rf var/cache/*
	@$(DOCKER) start $(DOCKER_DB_CONTAINER)
	@cd $(EZ_DIR) && $(CONSOLE) cache:clear
	@cd $(EZ_DIR) && $(SYMFONY) local:server:start -d --port=11083
	@cd $(EZ_DIR) && $(SYMFONY) run -d --watch=ezplatform/config,ezplatform/src,ezplatform/vendor,components symfony console messenger:consume ezaccelerator


.PHONY: stop
stop: ## Stop the web server if it is running
	@-cd $(EZ_DIR) && $(SYMFONY) local:server:stop
	@-$(DOCKER) stop $(DOCKER_DB_CONTAINER)


.PHONY: tests
tests: ## Run the tests
	@echo " ..:: Global Mono Repo Testing ::.."
	@PANTHER_NO_HEADLESS=${SHOW_CHROME} DATABASE_URL="mysql://root:ezplatform@127.0.0.1:3300/ezplatform" PANTHER_EXTERNAL_BASE_URI="https://127.0.0.1:11083" $(PHP_BIN) ./vendor/bin/phpunit -c "tests" "tests" --exclude-group behat
	@for COMPONENT in $(shell ls components); do \
    	if COMPONENT=$${COMPONENT} bin/ci-should test; then \
    		echo " ..:: Testing $${COMPONENT} ::.."; \
    		PANTHER_NO_HEADLESS=${SHOW_CHROME} DATABASE_URL="mysql://root:ezplatform@127.0.0.1:3300/ezplatform" PANTHER_EXTERNAL_BASE_URI="https://127.0.0.1:11083" $(PHP_BIN) ./vendor/bin/phpunit -c "components/$${COMPONENT}/tests" "components/$${COMPONENT}/tests" --exclude-group behat; \
		fi \
	done


.PHONY: ps
ps: ## Show docker-compose services
	@cd $(EZ_DIR) && $(SYMFONY) server:status
	@echo "\n!!!${RED}careful${RESTORE}!!!, if you change files outside the watched folders, you need to ${YELLOW}kill $PID${RESTORE} and re-rerun ${YELLOW}make consume${RESTORE}."


.PHONY: documentation
documentation: ## Generate the documention
	@$(SYMFONY) run --watch src,documentation/templates,components  bin/releaser doc -n

.PHONY: clean
clean: stop ## Removes the vendors, and caches
	@-rm -f .php_cs.cache
	@-rm -rf vendor
	@-rm -f drivers
	@-rm -rf ezplatform
	@-rm  node_modules
	@-$(DOCKER) rm $(DOCKER_DB_CONTAINER)
