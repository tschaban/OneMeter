<?php

if(file_exists("config/local/local.configuration.php"))
    include "config/local/local.configuration.php";

date_default_timezone_set('Europe/Warsaw');

define("DEBUG",true);

/* Pushover configuration */
define("PUSHOVER_ENABLED",true);
if (!defined("PUSHOVER_API_KEY")) define("PUSHOVER_API_KEY","");
if (!defined("PUSHOVER_USER_KEY")) define("PUSHOVER_USER_KEY","");

/* Configuration parameters to access InfluxDB 2 database */
if (!defined("INFLUXDB_URL")) define("INFLUXDB_URL","");         /* URL to the database */
if (!defined("INFLUXDB_TOKEN")) define("INFLUXDB_TOKEN","");     /* Access token */
if (!defined("INFLUXDB_ORG")) define("INFLUXDB_ORG","");         /* Org name */
if (!defined("INFLUXDB_ORG")) define("INFLUXDB_BUCKET","");      /* Bucket name */

/* Configuration parameters to access the OneMeter device data in https://cloud.onemeter.com/ */    
if (!defined("ONEMETER_DEVICE_ID")) define("ONEMETER_DEVICE_ID",""); /* Device ID */
if (!defined("ONEMETER_API_TOKEN")) define("ONEMETER_API_TOKEN",""); /* API Token */

/* Mappings of OneMeter Json data points */
define("ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_TOTAL","15_8_0");
define("ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T1","15_8_1");
define("ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T2","15_8_2");
define("ONEMETER_BATTERY_VOLTAGE","S_1_1_2");
define("ONEMETER_TEMPERATURE","S_1_1_9");
define("ONEMETER_READOUT_TIMESTAMP","date");

/* Openhab items names for specyfic oneMeter data points */
define("OPENHAB_ITEM_TOTAL","onemeter_total_kwh");
define("OPENHAB_ITEM_TOTAL_USED","onemeter_total_consumed_since_last_read_kwh");
define("OPENHAB_ITEM_TOTAL_T1","onemeter_total_tariff_1_kwh");
define("OPENHAB_ITEM_TOTAL_T1_USED","onemeter_total_tariff_1_consumed_since_last_read_kwh");
define("OPENHAB_ITEM_TOTAL_T2","onemeter_total_tariff_2_kwh");
define("OPENHAB_ITEM_TOTAL_T2_USED","onemeter_total_tariff_2_consumed_since_last_read_kwh");
define("OPENHAB_ITEM_BATTERY_VOLTAGE","onemeter_voltage");
define("OPENHAB_ITEM_TEMPERATURE","onemeter_temperature");

/* What data to delete from InfluxDB before upload the data */
$allItemsToDelete = array ( OPENHAB_ITEM_TOTAL,
                            OPENHAB_ITEM_TOTAL_USED,
                            OPENHAB_ITEM_TOTAL_T1,
                            OPENHAB_ITEM_TOTAL_T1_USED,
                            OPENHAB_ITEM_TOTAL_T2,
                            OPENHAB_ITEM_TOTAL_T2_USED,
                            OPENHAB_ITEM_BATTERY_VOLTAGE,
                            OPENHAB_ITEM_TEMPERATURE);

/* What data to upload to InfluxDB */
$allItemsToUpload = array ( OPENHAB_ITEM_TOTAL_USED,
                            OPENHAB_ITEM_TOTAL_T1_USED,
                            OPENHAB_ITEM_TOTAL_T2_USED,
                            OPENHAB_ITEM_BATTERY_VOLTAGE,
                            OPENHAB_ITEM_TEMPERATURE);


//$allItemsToUpload = array ( OPENHAB_ITEM_TOTAL_USED);
?>