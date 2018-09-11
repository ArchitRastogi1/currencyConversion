This is a currency conversion service -Refer to postman collection for more details. </br>

<b>Technologies Used - </b>  </br>

Programming – PHP </br>
Database – MySQL </br>
Cache – Redis </br>
Framework – Slim </br>
Libraries – Monolog </br>

<b>Database Tables - </b>  </br>

1 - ConversionHistoryLog – history log of all the conversion which maintains conversion rates along with a  historyId. </br>
2 - LatestCurrencyRates – latest conversion rates fetched by periodic job </br>
3 - CurrencyList – list of all the currencies </br>
4 – FailedQueue – keeps failed api record and re-run the jobs if required. // (optional) </br>


<b>Periodic Jobs - </b> </br>

1 – CurrencyRateFetcherJob -  This job will fetch latest conversion rates for one currency from  Xe.com and insert the conversion rates in Mysql tables – ConversionHistoryLog and LatestCurrencyRates. </br>

2 – CurrencyListFetcherJob – This job will fetch list of all the currencies from Xe.com and insert the currency rates in Mysql table – CurrencyList (This job will be run less frequently). </br>

<b>Endpoints - </b> </br>

1 – GET /currency-converter/from/<from-currency-code>/to/<to-currency-code>/amount/<amount> </br>
Response - </br>
200 – </br> {"from":"INR","to":"USD","amount":"100","convertedAmount":1.3868711980916,"historyLogId":"1536537600"} </br>
400 - </br>
{"message":"Currency Quantity is not Correct"} </br>
{"message":"From Currency Code is not Correct"} </br>
{"message":"To Currency Code is not Correct"} </br>

2 – GET /currency-converter/list/<currency-code> </br>
Response - </br>
200 -  </br>
[{"exchangeRates":2.1255127591,"currency":"FJD"}, {"exchangeRates":19.3286332708,"currency":"MXN"},{"exchangeRates":21151.950535817,"currency":"STD"},{"exchangeRates":13.6476390471,"currency":"SCR"},{"exchangeRates":1.4056440417,"currency":"TVD"},{"exchangeRates":1613.0276697267,"currency":"CDF"}] ... </br>
400 -  </br>
{"message":"Currency Code is not Correct"} </br>

<b> Caching - </b> </br>

1 – Cache the list of currencies. </br>
2 – Cache the conversion rates with respect to a base currency code. (In the database I am storing currency rates with respect to one currency only). </br>
3 – Cache currency conversion rates.  </br>
eg – USD_INR – 72.1034444 ,  USD_EURO – 0.9000044 </br>


<b> Folder Striucture - </b> </br>

src </br>
---- app </br>
      -------- commands (periodic jobs) </br>
      -------- daos (database access objects files) </br>
      -------- dependencies (dependency injection logic this will be cached in production environment) </br>
      -------- exceptions (exception handler and custom exceptions) </br>
      -------- models  </br>
      -------------- requests (api request objects) </br>
      -------------- configs (configuration files) </br>
	    -------------- errors (error codes) </br>
      -------------- tables (db tables object model) </br>
      -------------- resources (sql file and other resources) </br>
      -------------- routes  </br>
      ---------------------gets (get request routes – controllers) </br>
      --------------services  </br>
	    --------------------- conversionClients –  </br>
		  --------------------------- ConversionService (interface) </br>
      --------------------------- XeConversoon (implementation) – code is flexible to replace it with other service </br>
      ----------------------cacheClients -  (It has redisCache but code is flexible to replace it with other cache) </br>
		  ---------------------------- CacheService (interface) </br>
		  ---------------------------- RedisService (implementation) </br>
	    -------- services </br>
      -------- tests (unit tests) </br>
---- logs (to store loggin information) </br>
---- pubic </br>

<b> Points - </b> </br>
1 – Fetching and storing conversion rates for only one currency to save API calls and database space. </br>
2 – Caching to serve fast and not make much load on database tables. </br>
3 -  Keeping historyLogId to get back currency information from historyId if required. </br>
4 – Code is flexible to change conversion service and cache service. </br>
5 – Retry logic from code and queue (optional) to handle conversion service api failures. </br>

<b> Other Features to be add at server level - </b> </br>
1 – Add a load balancer to distriute traffic on multiple servers </br>
2 – Add a gateway which will handle request related things like – number of request a user can make to prevent misuse of system. </br>
3 – replcation of database tables to give high availability.	</br>

<b> How to Run this application </b> - <br/>
1 - Take Clone - https://github.com/ArchitRastogi1/currencyConversion.git <br/>
2 - cd currencyConversion <br/>
3-  change mysql and redis configuration in `src/app/models/configs/Configuration.php` <br/>
4 - php -S localhost:8080 <br/>
5 - http://localhost:8080/src/public/currency-converter/from/INR/to/USD/amount/100 <br/>
6 - http://localhost:8080/src/public/currency-converter/list/USD <br/>
