<?php
	
	$fileName  = 'filesFunctions.php';
    require_once $fileName;
	$action = $_POST['action'];
	if(empty($action)){
		header('400 Bad Request');
    	exit;
	}
	else{
		try{
			$filesManager = new FilesManager();
			
			switch ($action) {
				case 'drop':
					$filesArray = json_decode($_POST['filesArray']);
					echo json_encode($filesManager->filesDropper($filesArray));
					break;
				case 'filesList':
					echo json_encode($filesManager->fileList(1));
					break;
				case 'setPermission':
					echo json_encode($filesManager->setRecordsPermission());
					break;
				case 'fileCount':
					echo json_encode($filesManager->filesCount());
					break;
				case 'diskData':
					echo $filesManager->diskFreeSpace();
					break;
                case 'diskTotal':
                    echo $filesManager->diskTotalCapacity();
                    break;
                case 'convert':
                    $vFileName = json_decode($_POST['fileName']);
                    echo $filesManager->videoConverter($vFileName);
                    break;
				case 'returnIp':
					echo json_encode($filesManager->returnIp());
					break;
			}
		}
		catch(Exception $e){
			return '404 Not Found '.$e;
    		exit;
		}
	}
?>