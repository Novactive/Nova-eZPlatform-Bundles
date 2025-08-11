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
EZ_DIR := $(CURRENT_DIR)/ibexa
DOCKER := docker
DOCKER_DB_CONTAINER := dbezplbundl
MYSQL := mysql -f -u root -pezplatform -h 127.0.0.1 -P 3300 ezplatform
CONSOLE := $(PHP_BIN) bin/console
IBEXA_VERSION ?= 4.*
IBEXA_PACKAGE ?= oss
HOME_EDITIONS_DIR := .ddev/homeadditions
COMPOSER_AUTH_DIR := $(HOME_EDITIONS_DIR)/.composer
.DEFAULT_GOAL := list

.PHONY: list
list:
	@echo "******************************"
	@echo "${YELLOW}Available targets${RESTORE}:"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf " ${YELLOW}%-15s${RESTORE} > %s\n", $$1, $$2}'
	@echo "${RED}==============================${RESTORE}"

.PHONY: codeclean
codeclean: ## Coding Standard checks
	@ddev exec -d /var/www/html "$(PHP_BIN) ./vendor/bin/phpcbf --standard=.cs/cs_ruleset.xml --extensions=php src/ components/ bin/"
	@ddev exec -d /var/www/html "$(PHP_BIN) ./vendor/bin/php-cs-fixer fix --config=.cs/.php_cs.php"
	@ddev exec -d /var/www/html "$(PHP_BIN) ./vendor/bin/phpcs --standard=.cs/cs_ruleset.xml --extensions=php src/ components/ bin/"
	@ddev exec -d /var/www/html "$(PHP_BIN) ./vendor/bin/phpmd src,components,bin text .cs/md_ruleset.xml"

.PHONY: composer-auth
composer-auth: ## Test if the auth.json file exists
	@if [ -f ./auth.json ]; then \
		echo "Creating auth.json in ${COMPOSER_AUTH_DIR}..."; \
		mkdir -p ${COMPOSER_AUTH_DIR} && cp ./auth.json ${COMPOSER_AUTH_DIR}; \
		echo "Restarting ddev to take ddev for auth.json..."; \
		ddev restart; \
	else \
		echo "No auth.json auth.json in ${COMPOSER_AUTH_DIR}..."; \
	fi

.PHONY: install
install: composer-auth ## Install vendors
	@ddev exec -d /var/www/html "$(COMPOSER) install"


.PHONY: wrap-bundles
wrap-bundles:
	@echo "..:: Put Bundles in there ::.."
	@for COMPONENT in $(shell ls components); do \
		if COMPONENT=$${COMPONENT} bin/ci-should install; then \
    		echo " ..:: Installing $${COMPONENT} ::.."; \
			ddev exec -d /var/www/html "COMPONENT_CONFIG_DIR='components/$${COMPONENT}/tests/provisioning' COMPONENT=$${COMPONENT} bin/wrapbundle"; \
		fi \
	done

.PHONY: post-install
post-install: wrap-bundles
	@echo "..:: Do bundle YARN deps ::.."
	@ln -sf $(EZ_DIR)/node_modules
	@ddev exec "yarn add --dev algoliasearch react react-collapsible react-dom react-instantsearch-dom"

	@ddev exec "$(COMPOSER) update"
	@ddev exec "$(CONSOLE) d:s:u --force"

	@echo "..:: Do bundle specifics ::.."
	cat components/SEOBundle/bundle/Resources/sql/schema.sql | ddev mysql
	cat components/2FABundle/bundle/Resources/sql/schema.sql | ddev mysql

# TO BE ADDED BACK WHEN COMPLIANT WITH 4.x
#	@ddev exec "$(CONSOLE) novaezextra:contenttypes:create ../tests/vmcd.xlsx"
#	@ddev exec "$(CONSOLE) novaezprotectedcontent:install"
#	@ddev exec "$(CONSOLE) novaezhelptooltip:create"
#	@ddev exec "$(CONSOLE) doctrine:schema:update --dump-sql --force"
#	@ddev exec "$(CONSOLE) novaezmailing:install"
#	@cp -rp components/ProtectedContentBundle/tests/provisioning/article.html.twig $(EZ_DIR)/templates/themes/standard/full/
#	@cp -rp components/StaticTemplatesBundle/tests/provisioning/static_ultimatenova $(EZ_DIR)/templates/themes/

	@echo "..:: Final Cleaning Cache ::.."
	@ddev exec "$(CONSOLE) cache:clear"

.PHONY: installibexa
installibexa: install ## Install Ibexa as the local project
	echo "installing ibexa/${IBEXA_PACKAGE}-skeleton"
	@echo "..:: RUNNING $(COMPOSER) create-project 'ibexa/${IBEXA_PACKAGE}-skeleton:${IBEXA_VERSION}' --prefer-dist --no-progress --no-interaction ibexa ::.."
	@ddev exec -d /var/www/html "$(COMPOSER) create-project 'ibexa/${IBEXA_PACKAGE}-skeleton:${IBEXA_VERSION}' --prefer-dist --no-progress --no-interaction ibexa"
	@echo "..:: Do Ibexa Install ::.."

	@ddev exec "$(CONSOLE) ibexa:install"
	@ddev exec "$(CONSOLE) ibexa:graphql:generate-schema"
	@$(MAKE) post-install
	@ddev exec "$(COMPOSER) update"
	@ddev exec "$(COMPOSER) require -W phpunit/phpunit:^9.5 symfony/phpunit-bridge:^5.3"
	@rm -f $(EZ_DIR)/config/packages/test/doctrine.yaml

.PHONY: tests
tests: ## Run the tests
	@echo " ..:: Global Mono Repo Testing ::.."
	@ddev exec -d /var/www/html "PANTHER_NO_HEADLESS=${SHOW_CHROME} APP_ENV=test $(PHP_BIN) ./vendor/bin/phpunit -c 'tests' 'tests' --exclude-group behat"
	@for COMPONENT in $(shell ls components); do \
    	if COMPONENT=$${COMPONENT} bin/ci-should test; then \
    		echo " ..:: Testing $${COMPONENT} ::.."; \
    		ddev exec -d /var/www/html "PANTHER_NO_HEADLESS=${SHOW_CHROME} APP_ENV=test $(PHP_BIN) ./vendor/bin/phpunit -c 'components/$${COMPONENT}/tests' 'components/$${COMPONENT}/tests' --exclude-group behat"; \
    		ddev exec -d /var/www/html "PANTHER_NO_HEADLESS=${SHOW_CHROME} APP_ENV=test php ./vendor/bin/phpunit -c 'components/RssFeedBundle/tests' 'components/RssFeedBundle/tests' --exclude-group behat"; \
		fi \
	done

.PHONY: documentation
documentation: ## Generate the documention
	@ddev exec "$(SYMFONY) run --watch src,documentation/templates,components  bin/releaser doc -n"

.PHONY: clean
clean: ## Removes the vendors, and caches
	@ddev delete -O
	@-rm -f .php_cs.cache
	@-rm -rf vendor
	@-rm -rf drivers
	@-rm -rf $(EZ_DIR)
	@-rm  node_modules
