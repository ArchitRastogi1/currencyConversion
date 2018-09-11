<?php

namespace commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use services\CurrencyService;

class CurrencyListFetcherJob extends Command {
    
    private $currencyService;

    public function __construct(CurrencyService $currencyService) {
        $this->currencyService = $currencyService;
        parent::__construct('get:currency-list');
    }
    
    protected function configure() {
        $this
            ->setName('get:currency-list')
            ->setDescription('Periodically fetches currency lists');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $currencyCodeList = $this->currencyService->getCurrencyListFromClient();
        $this->currencyService->insertCurrencyList($currencyCodeList);
    }
    
}