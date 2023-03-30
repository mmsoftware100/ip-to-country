<?php

echo $_ENV['APP_NAME'];
die;

$ip = isset($_GET['ip']) ? $_GET['ip'] : "192.168.4.250";

if (filter_var($ip, FILTER_VALIDATE_IP)) {
    //echo("$ip is a valid IP address");
} else {
    //echo("$ip is not a valid IP address");
    echo "N/A";
    die;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "news";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  //echo "Connection failed: " . $e->getMessage();
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

$conn->close();





?>