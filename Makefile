.PHONY: coding-standards
coding-standards:
	 mkdir -p .build/php-cs-fixer
	 vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose
