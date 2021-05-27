# Purpose
Script leverages OneMeter https://onemeter.com/ device and data collected with OneMeter cloud

*The script:*
- Downloads electricity usage data from cloud.onemeter.com and uploads the data to InfluxDB data in regular frequency.
- It's primarly designed for InfluxDB, Grafana and openHAB (while this is not a blocker to use it with InfluxDB and Grafana only or with some adjustments with Domoticz
,HomeAssistant, etc)


## Highlevel description of how the script works and is configured

- It's configured to download the data from the last 24hrs. It's possible to change the time range of data
- There are all data records downloaded from onemeter cloud; currently, data stores electricity usage with 15m intervals
- Pre upload data n InfluxDB is deleted for the last 24hrs; can be configured
- Data mapping is made for G12 tariff
- The script is designed to upload the data to InfluxDB 2 database.
- The script may send the log after data upload to the cell phone using Pushover messaging solutions.

## What data are currently processed
- Total energy used
- Total energy used tariff 1
- Total energy used tariff 2
- Incremental consumption: tariff 1 (every 15m)
- Incremental consumption: tariff 2 (every 15m)
- OneMeter device battery voltage
- Temperature measured by OneMeter device

## Requirments

- OneMeter Device https://onemeter.com/
- Account at https://cloud.onemeter.com (to get the Device ID and API Token)
- InfluxDB 2: installed and configured
- PHP engine installed on a machine where the script is executed 
- 


### Optional configuration

- Pushover; used to sent push notification to cell phones: iOS, Android

## Hints for configuration:

- The script should be run in a defined frequency eg. every 24hrs
- to run the script use the *.sh or *.bat script depending on OS; requires adjustments in terms of paths

In case of questons, use gitHub issues or post it at https://forum.smartnydom.pl