#!/bin/bash

# Change to root folder (assuming this lies in scripts)
cd ..

updateOutput=`composer update 1>/dev/null`

if [[ $? -ne 0 ]]; then
	output=`composer install 2>&1`

	if [[ $? -ne 0 ]]; then
		echo "$output"
		exit 1;
	fi
fi

echo "$updateOutput"
exit 0;
