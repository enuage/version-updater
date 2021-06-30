test-fast:
	${MAKE} reset-test-data
	php vendor/bin/phpunit
	${MAKE} reset-test-data
.PHONY: test

test-coverage:
	${MAKE} reset-test-data
	php vendor/bin/phpunit --coverage-html=tests/output --coverage-text --colors=never
	${MAKE} reset-test-data
.PHONY: test

reset-test-data:
	git checkout tests/support/*
.PHONY: reset-test-data
