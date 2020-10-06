.PHONY: tests

all:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# QA

qa: cs phpstan ## Check code quality - coding style and static analysis

cs: ## Check PHP files coding style
	mkdir -p var/build-tools
	"vendor/bin/phpcs" src tests --standard=build/phpcs.xml $(ARGS)

csf: ## Fix PHP files coding style
	mkdir -p var/build-tools
	"vendor/bin/phpcbf" src tests --standard=build/phpcs.xml $(ARGS)

phpstan: ## Analyse code with PHPStan
	mkdir -p var/build-tools
	"vendor/bin/phpstan" analyse src -c build/phpstan.src.neon $(ARGS)
	"vendor/bin/phpstan" analyse tests -c build/phpstan.tests.neon $(ARGS)

# Tests

tests: ## Run all tests
	"vendor/bin/phpunit" -c build/phpunit.xml $(ARGS)

coverage-clover: ## Generate code coverage in XML format
	php -d pcov.enabled=1 -d pcov.directory=./src vendor/bin/phpunit -c build/phpunit.xml --coverage-clover var/coverage/clover.xml $(ARGS)

coverage-html: ## Generate code coverage in HTML format
	php -d pcov.enabled=1 -d pcov.directory=./src vendor/bin/phpunit -c build/phpunit.xml --coverage-html var/coverage/coverage-html $(ARGS)
