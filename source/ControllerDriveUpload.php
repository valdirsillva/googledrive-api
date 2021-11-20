<?php


namespace Source;

use Exception;

use Google_Client as GoogleDrive; 

use Google_Service_Drive;

use Google_Service_Drive_DriveFile;


class ControllerDriveUpload 
{
    private  $client;

    public function __construct(GoogleDrive $client) 
    {
       $this->client = $client;
    }

    public function setUpload() 
    {
       $form = filter_input_array(INPUT_POST, FILTER_DEFAULT);

       if ( isset($form['submit'])) {

          if ( empty($_FILES["file"]["tmp_name"])) {
               throw new Exception('Por favor selecionar uma imagem ');
               die;
          }

          $dataset = [
            "file_temp_name" =>  $_FILES["file"]["tmp_name"],
            "get_type_image" => $_FILES["file"]["type"],
            "filename" => basename($_FILES["file"]["name"])    
          ];

          $path = "uploads/".$dataset['filename'];
          $dataset['filePath'] = $path;


          if ( !move_uploaded_file($dataset['file_temp_name'], $path)) {
               throw new Exception('Não foi possíve concluir o upload do arquivo !');
          }

          move_uploaded_file ($dataset['file_temp_name'], $path);

          $dataset["folder_id"] = $this->createFolder("googledriver-app");

          if ($this->saveFileToDrive($dataset)) {

              echo "Upload efetuado com sucesso ! <br><br>";

          } else {
              echo "Erro ao tentar efetuar upload para o Drive";
          }
       }
    }

    private function createFolder(string $folder_name, $parent_folder_id=null): string
    {
        $listFolder = $this->checkFolderExists($folder_name);
       
        // se a pasta não existir 
        if ( is_countable($listFolder) && count($listFolder) == 0 ) {

            $service = new Google_Service_Drive( $this->client);
            $folder = new Google_Service_Drive_DriveFile();
            
            $folder->setName($folder_name);
            $folder->setMimeType('application/vnd.google-apps.folder');
            
            if ( !empty($parent_file_id) ) {
                 $file->setParents( [ $parent_file_id ] );      
            }

            $result = $service->files->create( $folder );
    
            $folder_id = null;
            
            if( isset( $result['id'] ) && !empty( $result['id'] ) ){
                $folder_id = $result['id'];
            }
         
            return $folder_id;

        }
       
        return $listFolder[0]['id'];

    }

    private function checkFolderExists(string $folderName): array 
    {
        $service = new Google_Service_Drive( $this->client );

        $parameters['q'] = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";
        $files = $service->files->listFiles($parameters);

        $option = [];

        foreach( $files as $k => $file ){
           $option[] = $file;
        }
       
        return $option;
    }


    private function saveFileToDrive( array $dataset ): bool 
    {
       $service = new Google_Service_Drive($this->client);
       $file =  new Google_Service_Drive_DriveFile();

       $file->setName($dataset['filename']);

       if ( !empty($dataset['folder_id']) ) {
           $file->setParents( [ $dataset['folder_id'] ] );      
       }

       $result = $service->files->create(
            $file, [
                    'data' => file_get_contents($dataset["filePath"]),
                    'mimeType' => 'application/octet-stream',
            ]
       );
 
      $isSuccess = false;
      
      if( isset( $result['name'] ) && !empty( $result['name'] ) ){
         $isSuccess = true;
      }
   
      return $isSuccess;
    }
}