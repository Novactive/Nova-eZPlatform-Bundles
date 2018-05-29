# === Makefile Helper ===

# Styles
YELLOW=$(shell echo "\033[00;33m")
RED=$(shell echo "\033[00;31m")
RESTORE=$(shell echo "\033[0m")

# Variables
PHP_BIN := php
DOCKER_BIN := docker
SRCS := src
CURRENT_DIR := $(shell pwd)
SCRIPS_DIR := $(CURRENT_DIR)/scripts

.DEFAULT_GOAL := list

# #####################################
# BEGIN - Project Specific starts here
# #####################################


# #####################################
# END
# #####################################
.PHONY: list
list:
	@echo "******************************"
	@echo "${YELLOW}Available targets${RESTORE}:"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf " ${YELLOW}%-15s${RESTORE} > %s\n", $$1, $$2}'
	@echo "${RED}==============================${RESTORE}"

.PHONY: codeclean
codeclean: ## Run the codechecker
	SCRIPS_DIR=$(SCRIPS_DIR) bash $(SCRIPS_DIR)/codechecker.bash

.PHONY: tests
tests: ## Run the tests
	SCRIPS_DIR=$(SCRIPS_DIR) bash $(SCRIPS_DIR)/runtests.bash

.PHONY: clean
clean: ## Removes the vendors, and caches
	rm -f .php_cs.cache
	rm -rf vendor
