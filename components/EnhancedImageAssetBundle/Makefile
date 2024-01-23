# === Makefile Helper ===

# Styles
YELLOW=$(shell echo "\033[00;33m")
RED=$(shell echo "\033[00;31m")
RESTORE=$(shell echo "\033[0m")

# Variables
PHP_BIN := php
COMPOSER := composer
SRCS := src
CURRENT_DIR := $(shell pwd)
PLANTUMLJAR := $(CURRENT_DIR)/plantuml.jar
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
	$(PHP_BIN) ./vendor/bin/phpmd src text .cs/md_ruleset.xml
	$(PHP_BIN) ./vendor/bin/phpcpd src
	$(PHP_BIN) ./vendor/bin/phpcbf -n src --standard=.cs/cs_ruleset.xml
	$(PHP_BIN) ./vendor/bin/phpcs --standard=.cs/cs_ruleset.xml --extensions=php src

.PHONY: install
install: ## Install vendors
	$(COMPOSER) install

.PHONY: clean
clean: ## Removes the vendors, and caches
	rm -f .php_cs.cache
	rm -rf vendor
	rm -f composer.lock

.PHONY: tests
tests: ## Execute tests
	$(PHP_BIN) ./vendor/bin/phpcs --standard=.cs/cs_ruleset.xml --extensions=php src/bundle
	$(PHP_BIN) ./vendor/bin/phpmd src/bundle text .cs/md_ruleset.xml
	$(PHP_BIN) ./vendor/bin/phpcpd src/bundle
