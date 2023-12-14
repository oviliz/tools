<?php $time_start = microtime(true);

// Add multiple IPs to blacklist, whitelist or unlist them on Cloudflare using CloudFlare API by AzzA <azza@broadcasthe.net>
// Ed Cooper 2015 - https://blog.ed.gs
// Version 1.0

// Configure your API key and email address below

$cfemailaddress = "REPLACEwithEMAILaddress"; // Cloudflare email address
$cfapikey = "REPLACEwithAPIkey"; // Cloudflare API key
$type = "Whitelist"; // Use either Whitelist, Blacklist or Unlist as options

// Use either $url, $ips or both, but make sure the URL address is a line break separated list of IPs, comment and uncomment the lines as needed

#$url = "https://managewp.com/wp-content/uploads/2016/11/managewp-ips.txt"; // Web address for line break separated list of IPs
$ips = array("35.162.254.253","52.11.12.231","52.11.29.70","52.11.54.161","52.24.142.159","52.25.191.255","52.27.181.126","52.34.126.117","52.34.254.47","52.35.82.99","52.36.28.80","52.38.106.97","52.39.177.152","52.41.230.148","52.41.237.12","52.42.126.166","52.43.13.71","52.43.76.224","52.88.96.110","52.89.155.51","54.148.73.118","54.186.37.105","54.187.92.57","54.191.32.65","54.191.67.23","54.191.80.119","54.191.135.209","54.191.136.176","54.191.137.17","54.191.148.85","54.191.149.8","52.26.122.21","52.24.187.29","52.89.85.107","54.186.128.167","54.191.40.136","52.24.62.11","52.88.119.122","54.191.148.225","54.191.151.18","52.89.94.121","52.25.116.116","52.88.215.225","54.186.143.184","52.88.197.180","52.27.171.126"); // Statuscake IPs

// STOP EDITING NOW

function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

class cloudflare_api
{
  // The URL of the API
  private static $URL = array(
    'USER' => 'https://www.cloudflare.com/api_json.html',
    'HOST' => 'https://api.cloudflare.com/host-gw.html'
  );

  // Timeout for the API requests in seconds
  const TIMEOUT = 5;

  // Stores the api key
  private $token_key;
  private $host_key;

  // Stores the email login
  private $email;

  /**
   * Make a new instance of the API client
   */
  public function __construct()
  {
    $parameters = func_get_args();
    switch (func_num_args()) {
      case 1:
        // a host API
        $this->host_key = $parameters[0];
        break;
      case 2:
        // a user request
        $this->email = $parameters[0];
        $this->token_key = $parameters[1];
        break;
    }
  }

  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function setToken($token_key)
  {
    $this->token_key = $token_key;
  }


  /**
   * CLIENT API
   * Section 3
   * Access
   */

  /**
   * 4.7a - Whitelist IPs
   * You can add an IP address to your whitelist.
   */
  public function wl($ip)
  {
    $data = array(
      'a'   => 'wl',
      'key' => $ip
    );
    return $this->http_post($data);
  }

  /**
   * 4.7b - Blacklist IPs
   * You can add an IP address to your blacklist.
   */
  public function ban($ip)
  {
    $data = array(
      'a'   => 'ban',
      'key' => $ip
    );
    return $this->http_post($data);
  }

  /**
   * 4.7c - Unlist IPs
   * You can remove an IP address from the whitelist and the blacklist.
   */
  public function nul($ip)
  {
    $data = array(
      'a'   => 'nul',
      'key' => $ip
    );
    return $this->http_post($data);
  }

  /**
   * GLOBAL API CALL
   * HTTP POST a specific task with the supplied data
   */
  private function http_post($data, $type = 'USER')
  {
    switch ($type) {
      case 'USER':
        $data['u']   = $this->email;
        $data['tkn'] = $this->token_key;
        break;
      case 'HOST':
        $data['host_key'] = $this->host_key;
        break;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_URL, self::$URL[$type]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $http_result = curl_exec($ch);
    $error     = curl_error($ch);
    $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http_code != 200) {
      return array(
        'error' => $error
      );
    } else {
      $result = json_decode($http_result);
      echo $result->{'response'}->{'result'}->{'ip'}." - ".ucfirst($result->{'result'})."\n";
    }
  }
}

$valid = array();

// Check if the URL is set in the parameters at the top then retrieve and validate the contents
if (isset($url)) {
  $url = get_data($url);

  $comma = strpos($url, ",");
  $space = strpos($url, " ");
  $linebreak = strpos($url, "\n");

  if ($comma !== false) {
    $url = explode(",", $url);
    echo "Comma detected\n";
  } else if ($space !== false) {
    $url = explode(" ", $url);
    echo "Space detected\n";
  } else if ($linebreak !== false) {
    $url = explode("\n", $url);
    echo "Line break detected\n";
  } else {
    echo "Can't detect delimiter, is it a space, comma or new line?\n";
  }
  if (is_array($url)) {
    foreach ($url as $contents) {
      if (filter_var($contents, FILTER_VALIDATE_IP)) {
        array_push($valid, $contents);
      } else {
        echo $contents." is not valid.\n";
      }
    }
  }
}

// Check if any IPs are set in the parameters and validate the contents
if (isset($ips)) {
  if (is_array($ips)) {
    foreach ($ips as $contents) {
      if (filter_var($contents, FILTER_VALIDATE_IP)) {
        array_push($valid, $contents);
      } else {
        echo $contents." is not valid.\n";
      }
    }
  }
}

// What have we got?
echo count($valid)." IPs detected. ".$type."ing with Cloudflare now...\n";

// Set the listing types
$checkVars = array("Whitelist","Blacklist","Unlist");

// Check the listing type and get started
if(in_array($type, $checkVars)){

  // Run the $url IPs first
  if (isset($url)) {
    if (is_array($url)) {
      foreach ($url as $value) {
        $cf = new cloudflare_api($cfemailaddress, $cfapikey);
        if($type == "Whitelist") {
          $response = $cf->wl($value);
        } elseif($type == "Blacklist") {
          $response = $cf->ban($value);
        } elseif($type == "Unlist") {
          $response = $cf->nul($value);
        }
        print_r($response);
      }
    }
  } else {
    echo "No URL specified, trying IPs\n";
  }

  // Run the $ips array second
  if (isset($ips)) {
    if (is_array($ips)) {
      foreach ($ips as $value) {
        $cf = new cloudflare_api($cfemailaddress, $cfapikey);
        if($type == "Whitelist") {
          $response = $cf->wl($value);
        } elseif($type == "Blacklist") {
          $response = $cf->ban($value);
        } elseif($type == "Unlist") {
          $response = $cf->nul($value);
        }
        print_r($response);
      }
    }
  } else {
    echo "No manual IPs specified\n";
  }

} else {
  echo "Unknown type, please check configuration\n";
}

// Fin
echo "Finished\n";
$time_end = microtime(true);

$execution_time = ($time_end - $time_start);

echo "Script running time:".round($execution_time)." seconds\n";
