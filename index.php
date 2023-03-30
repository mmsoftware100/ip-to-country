<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__FILE__));


$dotenv->load();


$debug = isset($_ENV['APP_DEBUG']) ? $_ENV['APP_DEBUG'] : false;



/*

if($debug == "true"){
    echo "it's true";
}
else{
    echo "it's false";
}
*/


//echo isset($_ENV['APP_NAMEa']) ? $_ENV['APP_NAMEa'] : "APP_NAMEa";

//echo "<h2>test</h2>";

//die;

$ip = isset($_GET['ip']) ? $_GET['ip'] : "127.0.0.1";

if (filter_var($ip, FILTER_VALIDATE_IP)) {
    if($debug == "true"){
        echo("$ip is a valid IP address");
    }
} else {
    if($debug == "true"){
        echo("$ip is not a valid IP address");
    }
    echo "N/A";
    die;
}

$servername = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : "localhost";
$username = isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : "roott";
$password = isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : "";
$database = isset($_ENV['DB_DATABASE']) ? $_ENV['DB_DATABASE'] : "news";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  if($debug == "true"){
        echo "Connection failed: " . $e->getMessage();
    }
  echo "N/A";
  die;
}

/*
$sql = "select country_iso_code
from (
  select *
  from geoip2_network
  where inet6_aton('146.243.121.22') >= network_start AND
	inet6_aton('146.243.121.22') <= network_end
  order by network_start desc
  limit 1
) net
left join geoip2_location location on (
  net.geoname_id = location.geoname_id and location.locale_code = 'en'
)";
*/
$sql = "select country_iso_code
from (
  select *
  from geoip2_network
  where inet6_aton('$ip') >= network_start AND
	inet6_aton('$ip') <= network_end
  order by network_start desc
  limit 1
) net
left join geoip2_location location on (
  net.geoname_id = location.geoname_id and location.locale_code = 'en'
)";


$stmt = $conn->prepare($sql);
$stmt->execute();

// set the resulting array to associative
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
$data = $stmt->fetchAll();
if(count($data) > 0){
    echo $data[0]['country_iso_code'];
}
else{
    echo "N/A";
}

?>