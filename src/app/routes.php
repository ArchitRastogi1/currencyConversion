<?php
$app->get('/currency-converter/from/{from}/to/{to}/amount/{amount}', '\routes\gets\CurrencyConverterController:convertCurrency');
$app->get('/currency-converter/list/{sourceCurrency}', '\routes\gets\CurrencyConverterController:currencyRateList');

