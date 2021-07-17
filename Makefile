_: list

# QA

qa: cs phpstan ## Check code quality - coding style and static analysis

cs: ## Check PHP files coding style
	mkdir -p var/tools/PHP_CodeSniffer
	$(PRE_PHP) "vendor/bin/phpcs" src tests --standard=tools/phpcs.xml $(ARGS)

csf: ## Fix PHP files coding style
	mkdir -p var/tools/PHP_CodeSniffer
	$(PRE_PHP) "vendor/bin/phpcbf" src tests --standard=tools/phpcs.xml $(ARGS)

phpstan: ## Analyse code with PHPStan
	mkdir -p var/tools
	$(PRE_PHP) "vendor/bin/phpstan" analyse src -c tools/phpstan.src.neon $(ARGS)
	$(PRE_PHP) "vendor/bin/phpstan" analyse tests -c tools/phpstan.tests.neon $(ARGS)

# Tests

.PHONY: tests
tests: ## Run all tests
	$(PRE_PHP) "vendor/bin/phpunit" -c tools/phpunit.xml $(ARGS)

coverage-clover: ## Generate code coverage in XML format
	$(PRE_PHP) $(PHPUNIT_COVERAGE) --coverage-clover=var/coverage/clover.xml $(ARGS)

coverage-html: ## Generate code coverage in HTML format
	$(PRE_PHP) $(PHPUNIT_COVERAGE) --coverage-html=var/coverage/coverage-html $(ARGS)

mutations: ## Check code for mutants
	$(PRE_PHP) $(PHPUNIT_COVERAGE) --coverage-xml=var/coverage/coverage-xml --log-junit=var/coverage/junit.xml
	$(PRE_PHP) vendor/bin/infection \
		--configuration=tools/infection.json \
		--threads=$(nproc) \
		--coverage=../var/coverage \
		--skip-initial-tests \
		$(ARGS)

# Utilities

list:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

PRE_PHP=XDEBUG_MODE=off

PHPUNIT_COVERAGE=php -d pcov.enabled=1 -d pcov.directory=./src vendor/phpunit/phpunit/phpunit -c tools/phpunit.xml
