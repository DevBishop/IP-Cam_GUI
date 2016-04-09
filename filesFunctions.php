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
	
	/*public function setRecordsPermission(){
		$count = 0;
        foreach($this->fileList(1) as $file){
            if(chmod($this->recordsPath().$file, 0777)){
            //if(chown($this->recordsPath().$file, 'www-data')){
                $count++;
            }
        }
        return $count;
	}
    
    public function setRecordsPermission(){
		shell_exec("chmod g+x -R /mnt/hdd/FTP/FI9828W_00626E55DA86/record\n");
    }*/
	
	public function diskManager(){
		$directory = $this->recordsPath();
		$io = popen ( '/usr/bin/du -sk ' . $directory, 'r' );
		$size = fgets ( $io, 4096);
		$size = substr ( $size, 0, strpos ( $size, "\t" ) );
		pclose ( $io );
		if($size<1024000){
			$size = round($size/1024);
			$size .= ' MB';
		}
		else{
			$size = round($size/1024000);
			$size .= ' GB';
		}
		return $size;
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