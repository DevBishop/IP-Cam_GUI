<?php
class FilesManager{

    private function recordsPath(){
        $path = simplexml_load_file('config.xml');
        return $path->recordsPath;
    }

    public function filesCount(){
		
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