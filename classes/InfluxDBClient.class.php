<?php
if (stripos($_SERVER['PHP_SELF'], "InfluxDBClient.class.php") == true) die();

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use InfluxDB2\Model\DeletePredicateRequest;
use InfluxDB2\Service\DefaultService;

class InfluxDBClient
{
    private $debug;
    private $client;
    private $service;
    private $writeApi;

    public function __construct()
    {
      global $debug;  
      $this->debug = $debug;

      $this->client = new Client([
        "url" => INFLUXDB_URL,
        "token" => INFLUXDB_TOKEN,
        "bucket" => INFLUXDB_BUCKET,
        "org" => INFLUXDB_ORG,
        "precision" => InfluxDB2\Model\WritePrecision::S
        ]);

        $this->service = $this->client->createService(DefaultService::class); 
        $this->writeApi = $this->client->createWriteApi();
    }

    function __destruct() {
      $this->client->close(); 
    }


    public function uploadItem($item,$value,$timestamp) {
      $this->debug->push("INFLUXDB: Uploading: Timestamp=".$timestamp.", Item: " . $item . ", Value: " . $value);
      $timestamp  = new DateTime($timestamp);
      $point=InfluxDB2\Point::measurement($item)
      ->addTag("item", $item)
      ->addField("value",$value)
      ->time($timestamp->getTimestamp());
      $this->writeApi->write($point,WritePrecision::S, INFLUXDB_BUCKET, INFLUXDB_ORG);

    }

   
    public function deleteItem ($item,$start,$end) {
      $this->debug->push("INFLUXDB: Removing data for item: ". $item . ", Range from: ". $start . " to: ".$end);
      $predicate = new DeletePredicateRequest();
      $predicate->setStart(new DateTime($start));
      $predicate->setStop(new DateTime($end));
      //$predicate->setStart(DateTime::createFromFormat('Y', '2020'));
      //$predicate->setStop(DateTime::createFromFormat('Y', '2022'));        
      $predicate->setPredicate("_measurement=\"".$item."\"");
      $this->service->deletePost($predicate, null, INFLUXDB_ORG, INFLUXDB_BUCKET);
    }


}
?>