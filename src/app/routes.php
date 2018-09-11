<?php
$app->get('/v1/currency-converter/from/{from}/to/{to}/amount/{amount}', '\routes\gets\CurrencyConverterController:convertCurrency');
$app->get('/v1/currency-converter/list/{sourceCurrency}', '\routes\gets\CurrencyConverterController:currencyRateList');

