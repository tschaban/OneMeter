<?php
require 'vendor/autoload.php';

require_once ("classes/debugger.class.php");
require_once ("classes/InfluxDBClient.class.php");
require_once ("config/configuration.php");

if (PUSHOVER_ENABLED)
{
    require_once ("classes/pushoverAPI.class.php");
    require_once ("classes/pushover.class.php");
}

$debug = new debugger(DEBUG);
$debug->push("OneMeter: Starting script to download yesterday's onemeter readings");

$_startUpload = date('Y-m-dT00:00:00.000Z', strtotime("-1 day"));
$_startAdjusted = date('Y-m-dT23:45:00.000Z', strtotime("-2 day")); // needs additional one readout pre the first readout to upload. It should be 15m earlier than $_start_delete
$_endUpload = date('Y-m-dT23:59:59.999Z', strtotime("-1 day"));

/* Custom upload */
//$_startUpload = "2021-03-01T12:00:00.000Z";
//$_startAdjusted = "2021-03-01T12:00:00.000Z";
//$_endUpload =   "2021-04-04T15:00:00.000Z";


$_startAdjustedDateTime = new DateTime($_startUpload);
$_startUploadDateTime = new DateTime($_startAdjusted);
$_endtUploadDateTime = new DateTime($_endUpload);

$_startUpload = gmdate('Y-m-d\TH:i:s.000\Z', $_startAdjustedDateTime->getTimestamp());
$_startAdjusted = gmdate('Y-m-d\TH:i:s.000\Z', $_startUploadDateTime->getTimestamp());
$_endUpload = gmdate('Y-m-d\TH:i:s.999\Z', $_endtUploadDateTime->getTimestamp());

$debug->push(" : Range start: " . $_startUpload);
$debug->push(" : Range end: " . $_endUpload);

$_url = "https://cloud.onemeter.com/api/devices/" . ONEMETER_DEVICE_ID . "/readings?locale=pl&from=" . $_startAdjusted . "&to=" . $_endUpload;
$_header = array(
    "Authorization: " . ONEMETER_API_TOKEN
);

$error = "";

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
curl_setopt($ch, CURLOPT_URL, $_url);

$debug->push("OneMeter: reading...", false);
$result = curl_exec($ch);
curl_close($ch);
$debug->push("completed");

if (!$result)
{
    $error = "ERROR: OneMeter: Problem with downloading data";
    $debug->push($error);
}
else
{

    $json = json_decode(utf8_encode($result));

    if (empty($json) || json_last_error() !== JSON_ERROR_NONE)
    {
        $error = "ERROR: OneMeter: No data found or wrong JSON";
        $debug->push($error);
    }
    else
    {
        $debug->push("OneMeter: Processing data...");

        if (PUSHOVER_ENABLED)
        {
            $pushoverData[OPENHAB_ITEM_TOTAL_USED] = 0;
            $pushoverData[OPENHAB_ITEM_TOTAL_T1_USED] = 0;
            $pushoverData[OPENHAB_ITEM_TOTAL_T2_USED] = 0;
        }

        for ($i = 0;$i < count($json->{"readings"});$i++)
        {
            $debug->push("Processing record: " . $i);

            // Workarund for missing values; uses for calculation last known value
            if (!empty($json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_TOTAL}))
            {
                $lastKnown[OPENHAB_ITEM_TOTAL] = $json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_TOTAL};
            }

            if (!empty($json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T1}))
            {
                $lastKnown[OPENHAB_ITEM_TOTAL_T1] = $json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T1};
            }

            if (!empty($json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T2}))
            {
                $lastKnown[OPENHAB_ITEM_TOTAL_T2] = $json->{"readings"}[$i]->{ONEMETER_OBIS_ABSOLUTE_ACTIVE_ENERGY_T2};
            }

            if (!empty($json->{"readings"}[$i]->{ONEMETER_BATTERY_VOLTAGE}) || $json->{"readings"}[$i]->{ONEMETER_BATTERY_VOLTAGE} > 0)
            {
                $lastKnown[ONEMETER_BATTERY_VOLTAGE] = $json->{"readings"}[$i]->{ONEMETER_BATTERY_VOLTAGE};
            }

            if (!empty($json->{"readings"}[$i]->{ONEMETER_TEMPERATURE}))
            {
                $lastKnown[ONEMETER_TEMPERATURE] = $json->{"readings"}[$i]->{ONEMETER_TEMPERATURE};
            }

            $readout[$i][OPENHAB_ITEM_TOTAL] = $lastKnown[OPENHAB_ITEM_TOTAL];
            $readout[$i][OPENHAB_ITEM_TOTAL_T1] = $lastKnown[OPENHAB_ITEM_TOTAL_T1];
            $readout[$i][OPENHAB_ITEM_TOTAL_T2] = $lastKnown[OPENHAB_ITEM_TOTAL_T2];

            if ($i > 0)
            {
                $readout[$i][OPENHAB_ITEM_TOTAL_USED] = $readout[$i][OPENHAB_ITEM_TOTAL] - $readout[$i - 1][OPENHAB_ITEM_TOTAL];
                $readout[$i][OPENHAB_ITEM_TOTAL_T1_USED] = $readout[$i][OPENHAB_ITEM_TOTAL_T1] - $readout[$i - 1][OPENHAB_ITEM_TOTAL_T1];
                $readout[$i][OPENHAB_ITEM_TOTAL_T2_USED] = $readout[$i][OPENHAB_ITEM_TOTAL_T2] - $readout[$i - 1][OPENHAB_ITEM_TOTAL_T2];

                if (PUSHOVER_ENABLED)
                {
                    $pushoverData[OPENHAB_ITEM_TOTAL_USED] += $readout[$i][OPENHAB_ITEM_TOTAL_USED];
                    $pushoverData[OPENHAB_ITEM_TOTAL_T1_USED] += $readout[$i][OPENHAB_ITEM_TOTAL_T1_USED];
                    $pushoverData[OPENHAB_ITEM_TOTAL_T2_USED] += $readout[$i][OPENHAB_ITEM_TOTAL_T2_USED];
                }

            }
            $readout[$i][OPENHAB_ITEM_TEMPERATURE] = $lastKnown[ONEMETER_TEMPERATURE];
            $readout[$i][OPENHAB_ITEM_BATTERY_VOLTAGE] = $lastKnown[ONEMETER_BATTERY_VOLTAGE];
            $readout[$i]["timestamp"] = $json->{"readings"}[$i]->{ONEMETER_READOUT_TIMESTAMP};

        }
        $debug->push("completed");

        if (count($readout) > 1)
        {

            $influxClient = new InfluxDBClient();

            $debug->push("OneMeter: Loading data to InfluxDB");

            for ($_itmes = 0;$_itmes < count($allItemsToDelete);$_itmes++)
            {
                $influxClient->deleteItem($allItemsToDelete[$_itmes], $_startUpload, $_endUpload);
            }

            $debug->push("OneMeter: Loading data to InfluxDB");

            for ($i = 1;$i < count($readout);$i++)
            {

                for ($_itmes = 0;$_itmes < count($allItemsToUpload);$_itmes++)
                {
                    settype($readout[$i][$allItemsToUpload[$_itmes]], "double");
                    $influxClient->uploadItem($allItemsToUpload[$_itmes], $readout[$i][$allItemsToUpload[$_itmes]], $readout[$i]["timestamp"]);
                }

            }

        }
        else
        {
            $error = "WARN: OneMeter: No data to load InfluxDB";
            $debug->push($error);
        }

        if (PUSHOVER_ENABLED)
        {
            $Notification = new pushover();
            if ($error != "")
            {
                $Notification->send("OneMeter", "Data upload not finished\n" . $error);
            }
            else
            {
                $Notification->send("OneMeter", "Data uploaded\n" . "Total: " . number_format($pushoverData[OPENHAB_ITEM_TOTAL_USED], 2, '.', '') . "kWh\n" . "T1: " . number_format($pushoverData[OPENHAB_ITEM_TOTAL_T1_USED], 2, '.', '') . "kWh\n" . "T2: " . number_format($pushoverData[OPENHAB_ITEM_TOTAL_T2_USED], 2, '.', '') . "kWh\n" . "Period:\n" . "From: " . str_replace("T", " ", substr($_startUpload, 0, 16)) . "\nTo: " . str_replace("T", " ", substr($_endUpload, 0, 16)));
            }
        }
    }
}

$debug->push("OneMeter: Script completed!");

?>
