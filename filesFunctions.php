<?php
class FilesManager{

    private function recordsPath(){
        $path = simplexml_load_file('config.xml');
        return $path->recordsPath;
    }

    public function filesCount(){
		$this->returnIp();//chiamata a scopo di log
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
    
    function logger($parameter){
        $myFile = date("Ymd")."_log.log";
		$fh = fopen($myFile, 'a') or die("can't open file");
        $txt = date('l jS \of F Y h:i:s A')." -- [IP -> ".$parameter."]";
		fwrite($fh, $txt ."\n");
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
            $localUser = true;
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        $this->logger($ip);
        
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
}
?>