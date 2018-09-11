<?php

namespace commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use models\Currency;
use services\CurrencyService;

class CurrencyRatesFetcherJob extends Command {
    
    private $currencyService;

    public function __construct(CurrencyService $currencyService) {
        $this->currencyService = $currencyService;
        parent::__construct('get:currency-rates');
    }
    
    protected function configure() {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('get:currency-rates')
            // the short description shown while running "php bin/console list"
            ->setDescription('Periodically fetches currency rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $currencyCodeList = $this->currencyService->getCurrencyCodesList();
        
        $sourceCurrencyCode = Currency::BASE_CURRENCY;
        $targetCurrencyCodes = implode(",", $currencyCodeList);
        
        $exchangeRatesList = $this->currencyService->getExchangeRatesFromClient($sourceCurrencyCode, $targetCurrencyCodes);
        $this->currencyService->insertExchangeRatesInConversionHistoryLog($exchangeRatesList);
        $this->currencyService->insertExchangeRatesInLatestCurrencyRates($exchangeRatesList);
        
        $output->writeln("Currency Rates have been updated");
    }
    
}