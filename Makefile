.PHONY: tests

all:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# QA

qa: cs phpstan ## Check code quality - coding style and static analysis

cs: ## Check PHP files coding style
	mkdir -p var/tools/PHP_CodeSniffer
	"vendor/bin/phpcs" src tests --standard=tools/phpcs.xml $(ARGS)

csf: ## Fix PHP files coding style
	mkdir -p var/tools/PHP_CodeSniffer
	"vendor/bin/phpcbf" src tests --standard=tools/phpcs.xml $(ARGS)

phpstan: ## Analyse code with PHPStan
	mkdir -p var/tools
	"vendor/bin/phpstan" analyse src -c tools/phpstan.src.neon $(ARGS)
	"vendor/bin/phpstan" analyse tests -c tools/phpstan.tests.neon $(ARGS)

# Tests

tests: ## Run all tests
	"vendor/bin/phpunit" -c tools/phpunit.xml $(ARGS)

coverage-clover: ## Generate code coverage in XML format
	php -d pcov.enabled=1 -d pcov.directory=./src vendor/phpunit/phpunit/phpunit -c tools/phpunit.xml --coverage-clover var/coverage/clover.xml $(ARGS)

coverage-html: ## Generate code coverage in HTML format
	php -d pcov.enabled=1 -d pcov.directory=./src vendor/phpunit/phpunit/phpunit -c tools/phpunit.xml --coverage-html var/coverage/coverage-html $(ARGS)

mutations: ## Check code for mutants
	php -d pcov.enabled=1 -d pcov.directory=./src vendor/phpunit/phpunit/phpunit -c tools/phpunit.xml --coverage-xml=var/coverage/coverage-xml --log-junit=var/coverage/junit.xml
	vendor/bin/infection \
		--configuration=tools/infection.json \
		--threads=$(nproc) \
		--coverage=../var/coverage \
		--skip-initial-tests \
		$(ARGS)
