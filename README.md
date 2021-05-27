# Purpose
Downloads electricity usage data from cloud.onemeter.com and upload the data to InfluxDB data in regular frequency.
Designed for openHAB
Storage: InfluxDB 2

## Highlevel description of how the script works and is configured

- It's configured to download the data from the last 24hrs. It's, of course possible to change the time range of data
- Pre upload data n InfluxDB is deleted for the last 24hrs (can be configured)
- Data mapping is made for G12 tariff
- The script is designed to upload the data to InfluxDB 2 database.
- The script may send the log after data upload to the cell phone using Pushover messaging solutions.

## Requirments

- InfluxDB 2
- PHP engine installed on a machine where the script is executed 

## Hints for configuration:

The script should be run in a defined frequency eg. every 24hrs






