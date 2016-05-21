<?php
class FilesManager{

    private function recordsPath(){
        $path = simplexml_load_file('config.xml');
        return $path->recordsPath;
    }

    public function filesCount(){
		$this->returnUserLocationArray();//chiamata a scopo di log
		$directory = $this->recordsPath();
		
        $files = glob($directory . "*.{mkv,mp4}", GLOB_BRACE);
        if ( $files !== false )
        {
            $filecount = count( $files );
            return $filecount;
        }
        else
        {
            return 1;
        }
     
	}
    
    private function logger($parameter, $localUser){
        
        $myFile = date("Ymd")."_log.log";
		$fh = fopen($myFile, 'a') or die("can't open file");
        
        $iptxt = date("\n\n".'l jS \of F Y h:i:s A')." -- [IP -> ".$parameter."] -- [IsLocal -> ".$localUser."] ";
        
        $ua=$this->getBrowser();
        $browserTxt= "Your browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
        
		fwrite($fh, $iptxt ."\n\n");
        fwrite($fh, $browserTxt ."\n\n");
		fclose($fh);
    }
    
    public function returnUserLocationArray(){
        
        $localUser = false;
        
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        if (strpos($ip, '10.0.1.') !== false) {
            $localUser = true;
        }
        $this->logger($ip, $localUser);
        
        $userLocationArray = [
            "ip" => $ip,
            "isLocal" => $localUser,
        ];
        return $userLocationArray;
    }
	
	public function fileList($order){
		return scandir($this->recordsPath(), $order);
	}
    
    public function diskFreeSpace(){
        return $this->getSymbolByQuantity(disk_total_space($this->recordsPath())-disk_free_space($this->recordsPath()));
    }
    
    public function diskTotalCapacity(){
         return $this->getSymbolByQuantity(disk_total_space($this->recordsPath()));
    }
    
   private function getSymbolByQuantity($Bytes) {
        $Type = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');;
        $Index=0;
        while($Bytes>=1024)
        {
            $Bytes/=1024;
            $Index++;
        }
        return sprintf('%1.2f' ,"".$Bytes)." ".$Type[$Index];
    }
	
	public function filesDropper($filesArray){
		$count = 0;
		foreach($filesArray as $fileName){  
			if(unlink($this->recordsPath().$fileName)){
				$count++;
			}
		}
		return $count; 
	}
    
    public function videoConverter($vFileName){
        $file = str_replace("mkv", "mp4", $vFileName);
        shell_exec("ffmpeg -i ".$this->recordsPath().$vFileName." -vcodec copy -acodec copy -absf aac_adtstoasc ".$this->recordsPath().$file."");
        unlink($this->recordsPath().$vFileName);
        return $file;
       
    }
    
   
    function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 


}
?>