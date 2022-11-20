# Nexo Pro connector for PHP

## Install using composer ##

    composer require stilmark/nexo-pro-php

Set your API KEY and API SECRET in the .env file.

	NEXOPRO_API_URL=pro-api.prime-nexo.net
	NEXOPRO_API_VERSION=v1
	NEXOPRO_API_KEY=[YOUR_API_KEY]
	NEXOPRO_API_SECRET=[YOUR_API_SECRET]

## Basic usage ##

### Get account balances ###
	$balances = NexoPro::getAccountSummary();

### Get pair quote ###
	$quote = NexoPro::getQuote(
		pair: 'BTC/USDT',
		amount: 0.001,
		side: 'buy'
	);

---

Inspired by NexoPro connectors for:

TypeScript/Node.js : https://github.com/aussedatlo/nexo-pro

Python : https://github.com/guilyx/python-nexo