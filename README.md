# Purpose
-  download of electricity usage data from cloud.onemeter.com and upload the data to InfluxDB data in regular frequency.
- Designed for openHAB (while this is not a blocker to use it with InfluxDB and Grafana only or with some adjustments with Domoticz
,HomeAssistant, etc)
- Storage: InfluxDB 2

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

- InfluxDB 2: installed and configured
- PHP engine installed on a machine where the script is executed 

### Optional configuration

- Pushover; used to sent push notification to cell phones: iOS, Android

## Hints for configuration:

- The script should be run in a defined frequency eg. every 24hrs

In case of questons use gitHub issues or post it at https://forum.smartnydom.pl






