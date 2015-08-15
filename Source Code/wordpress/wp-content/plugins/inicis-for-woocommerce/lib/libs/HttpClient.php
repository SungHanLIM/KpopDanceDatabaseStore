<?php
define (CONNECT_TIMEOUT, 5);
define (READ_TIMEOUT, 15);

$explode_data = explode('/', $P_REQ_URL);  
$host = $explode_data[2];                  
$path = "/" . $explode_data[3] . "/" . $explode_data[4]; 

class HttpClient 
{
  var $sock=0;
  var $ssl;
  var $host;
  var $port;
  var $status;
  var $headers="";
  var $body="";
  var $reqeust;
  var $errorcode;
  var $errormsg;

  function HttpClient($ssl, $host) 
  {
		if($ssl=="true") 
		{
			$this->ssl = "ssl://";
			$this->port = 443;
		}
		$this->host = $host;
  }

	function HttpConnect()
	{
		if (!$this->sock = @fsockopen($this->ssl.$this->host, $this->port, $errno, $errstr, CONNECT_TIMEOUT))
		{
			$this->errorcode = $errno;
      switch($errno) 
			{
      	case -3:
        	$this->errormsg = 'Socket creation failed (-3)';
        case -4:
          $this->errormsg = 'DNS lookup failure (-4)';
        case -5:
          $this->errormsg = 'Connection refused or timed out (-5)';
        default:
          $this->errormsg = 'Connection failed ('.$errno.')';
          $this->errormsg .= ' '.$errstr;
      }
			return false;
    }
		return true;
	}

	function HttpRequest($uri, $data)
	{
   	$this->headers="";
   	$this->body="";

		/*Write*/
		$request  = "POST ".$uri." HTTP/1.0\r\n";
		$request .= "Connection: close\r\n";
		$request .= "Host: ".$this->host."\r\n";
		$request .= "Content-type: application/x-www-form-urlencoded\r\n";
		$request .= "Content-length: ".strlen($data)."\r\n";
		$request .= "Accept: */*\r\n";
		$request .= "\r\n";
		$request .= $data."\r\n";
		$request .= "\r\n";
		fwrite($this->sock, $request);
		
		/*Read*/
		stream_set_blocking($this->sock, FALSE ); 
		$atStart = true;
		$IsHeader = true;
		$timeout = false;
		$start_time= time();
		while ( !feof($this->sock) && !$timeout )
		{
			$line = fgets($this->sock, 4096);
			$diff=time()-$start_time;
			if( $diff >= READ_TIMEOUT)
			{
				$timeout = true;
			}
			if( $IsHeader )
			{
				if( $line == "" ) 
				{
					continue;
				}
				if( substr( $line, 0, 2 ) == "\r\n" )  
				{
					$IsHeader = false;
					continue;
				}
  				$this->headers .= $line;
            	if ($atStart) 
				{
                	$atStart = false;
                	if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) 
					{
                    	$this->errormsg = "Status code line invalid: ".htmlentities($line);
						fclose( $this->sock );
                    	return false;
                	}
                	$http_version = $m[1];
                	$this->status = $m[2];
                	$status_string = $m[3];
                	continue;
				}
            }
			else
			{
 				$this->body .= $line;
			}
		}
		fclose( $this->sock );

		if( $timeout )
		{
			$this->errorcode = READ_TIMEOUT_ERR;
            $this->errormsg = "Socket Timeout(".$diff."SEC)";
			return false;
		}
		return true;
	}

	function getErrorCode()
	{
		return $this->errorcode;
	}
	
	function getErrorMsg()
	{
		return $this->errormsg;
	}

    function getBody() 
	{
        return $this->body;
    }

}
?>
