<?php

namespace daos;
use Monolog\Logger;
use PDOException;
use Exception;
use PDO;
use models\configs\DatabaseConfiguration;

class CurrencyDao {
    
    private $logger;
    private $db;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
        if(empty($this->db)) {
            // this configuration will be moved to a yaml file.
            $this->db = new PDO(DatabaseConfiguration::MYSQL_DSN, DatabaseConfiguration::MYSQL_USER, DatabaseConfiguration::MYSQL_PASSWORD);
        }
    }
    
    public function getLatestExchangeRates($sourceCurrency, $targetCurrency) {
        try {
            $sqlStmt = "select exchangeRates,historyLogId,targetCurrency from LatestCurrencyRates where targetCurrency in (:currency1, :currency2)";
            $prepareStmt = $this->db->prepare($sqlStmt);
            $prepareStmt->bindValue(":currency1", $sourceCurrency, PDO::PARAM_STR);
            $prepareStmt->bindValue(":currency2", $targetCurrency, PDO::PARAM_STR);
            $prepareStmt->execute();
            $exchangeRates = $prepareStmt->fetchAll(PDO::FETCH_ASSOC);
            $prepareStmt->closeCursor();
            return $exchangeRates;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in fetching data from database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
    
    public function insertExchangeRatesInLatestCurrencyRates($currencyRatesArray) {
        try {
            $count = count($currencyRatesArray);
            $sqlStmt = "replace into LatestCurrencyRates (sourceCurrency, targetCurrency, exchangeRates, pollingTime, historyLogId) values ";
            for($i=0;$i<$count;$i++) {
                $sqlStmt.= "(:sourceCurrency_$i, :targetCurrency_$i, :exchangeRates_$i, :pollingTime_$i, :historyLogId_$i),";
            }
            $sqlStmt = rtrim($sqlStmt, ",");
            $prepareStmt = $this->db->prepare($sqlStmt);
            $i=0;
            foreach($currencyRatesArray as $currencyRates) {
                $prepareStmt->bindValue(":sourceCurrency_".$i, $currencyRates->getSourceCurrency(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":targetCurrency_".$i, $currencyRates->getTargetCurrency(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":exchangeRates_".$i, $currencyRates->getExchangeRates(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":pollingTime_".$i, $currencyRates->getPollingTime(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":historyLogId_".$i, $currencyRates->getHistoryLogId(), PDO::PARAM_STR);
                $i++;
            }
            $prepareStmt->execute();
            return true;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in updating data to database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
    
    public function insertCurrencyList($currencyList) {
        try {
            $count = count($currencyList);
            $sqlStmt = "replace into CurrencyList (currencyCode, currencyName, isObsolete) values ";
            for($i=0;$i<$count;$i++) {
                $sqlStmt .= "(:currencyCode_$i, :currencyName_$i, :isObsolete_$i),";
            }
            $sqlStmt = rtrim($sqlStmt,",");
            $prepareStmt = $this->db->prepare($sqlStmt);
            $i=0;
            foreach($currencyList as $currency) {
                $prepareStmt->bindValue(":currencyCode_".$i,$currency->getCurrencyCode(),PDO::PARAM_STR);
                $prepareStmt->bindValue(":currencyName_".$i,$currency->getCurrencyName(),PDO::PARAM_STR);
                $prepareStmt->bindValue(":isObsolete_".$i,$currency->getIsObsolete(),PDO::PARAM_INT);
                $i++;
            }
            $prepareStmt->execute();
            $prepareStmt->closeCursor();
            return true;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in updating data from database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
    
    public function getCurrencyCodesList() {
        try {
            $sqlStmt = "select currencyCode from CurrencyList";
            $prepareStmt = $this->db->prepare($sqlStmt);
            $prepareStmt->execute();
            $currencyList = $prepareStmt->fetchAll(PDO::FETCH_COLUMN);
            $prepareStmt->closeCursor();
            return $currencyList;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in fetching data from database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
    
    public function getCurrencyRateList($sourceCurrency) {
        try {
            $sqlStmt = "select * from LatestCurrencyRates where sourceCurrency = :sourceCurrency";
            $prepareStmt = $this->db->prepare($sqlStmt);
            $prepareStmt->bindValue("sourceCurrency", $sourceCurrency, PDO::PARAM_STR);
            $prepareStmt->execute();
            $currencyRateList = $prepareStmt->fetchAll(PDO::FETCH_ASSOC);
            $prepareStmt->closeCursor();
            return $currencyRateList;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in fetching data from database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
    
    public function insertExchangeRatesInConversionHistoryLog($exchangeRatesList) {
        try {
            $count = count($exchangeRatesList);
            $sqlStmt = "insert into ConversionHistoryLog (sourceCurrency, targetCurrency, exchangeRates, pollingTime, historyLogId) values ";
            for($i=0;$i<$count;$i++) {
                $sqlStmt.= "(:sourceCurrency_$i, :targetCurrency_$i, :exchangeRates_$i, :pollingTime_$i, :historyLogId_$i),";
            }
            $sqlStmt = rtrim($sqlStmt, ",");
            $prepareStmt = $this->db->prepare($sqlStmt);
            $i=0;
            foreach($exchangeRatesList as $currencyRates) {
                $prepareStmt->bindValue(":sourceCurrency_".$i, $currencyRates->getSourceCurrency(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":targetCurrency_".$i, $currencyRates->getTargetCurrency(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":exchangeRates_".$i, $currencyRates->getExchangeRates(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":pollingTime_".$i, $currencyRates->getPollingTime(), PDO::PARAM_STR);
                $prepareStmt->bindValue(":historyLogId_".$i, $currencyRates->getHistoryLogId(), PDO::PARAM_STR);
                $i++;
            }
            $prepareStmt->execute();
            $prepareStmt->closeCursor();
            return true;
        } catch(PDOException $ex) {
            $this->logger->addError("Error in updating data to database");
            throw $ex;
        } catch (Exception $ex) {
            $this->logger->addError("Some internal error occured");
            throw $ex;
        }
    }
}

