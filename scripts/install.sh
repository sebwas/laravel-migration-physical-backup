#!/bin/bash

if [[ -f vendor/composer.lock ]]; then
	cp vendor/composer.lock ./
fi

updateOutput=`composer update 1>/dev/null`

if [[ $? -ne 0 ]]; then
	output=`composer install 2>&1`

	if [[ $? -ne 0 ]]; then
		echo "$output"
		exit 1;
	fi

	echo "$output"
	exit 0;
fi

echo "$updateOutput"
exit 0;
