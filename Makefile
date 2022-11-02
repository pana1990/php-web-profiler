cs-fix:
	 mkdir -p .build/php-cs-fixer
	 composer run-script cs:fix

cs-check:
	 mkdir -p .build/php-cs-fixer
	 composer run-script cs:check
