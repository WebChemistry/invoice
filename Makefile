.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa: phpstan cs

cs:
	vendor/bin/codesniffer src tests

csf:
	vendor/bin/codefixer src tests

phpstan:
	vendor/bin/phpstan analyse -l max src

tests:
	vendor/bin/codecept run

coverage-clover:
	vendor/bin/codecept run --coverage --coverage-xml

coverage-html:
	vendor/bin/codecept run --coverage --coverage-html
