/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  archit
 * Created: 10 Sep, 2018
 */
CREATE TABLE ConversionHistoryLog (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sourceCurrency char(5) NOT NULL,
    targetCurrency char(5) NOT NULL,
    exchangeRates double NOT NULL,
    pollingTime timestamp NOT NULL,
    historyLogId varchar(255) NOT NULL,
    key `currency_key` (`historyLogId`,`targetCurrency`)
)ENGINE=innodb;

CREATE TABLE LatestCurrencyRates (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sourceCurrency char(5) NOT NULL,
    targetCurrency char(5) NOT NULL,
    exchangeRates double NOT NULL,
    pollingTime timestamp NOT NULL,
    historyLogId varchar(255) NOT NULL,
    UNIQUE KEY `currency_key` (`targetCurrency`,`sourceCurrency`)
)ENGINE=innodb;

CREATE TABLE CurrencyList (
    currencyCode char(5) NOT NULL PRIMARY KEY,
    currencyName char(100) NOT NULL,
    isObsolete tinyint(1) default 0
)ENGINE=innodb;

/*optional
CREATE TABLE Queue (
)
*/