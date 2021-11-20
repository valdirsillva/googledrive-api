<?php


use Source\ControllerDriveUpload;

require "googledrive-run.php";

$client = getClient();
$driveUpload = new ControllerDriveUpload($client);

$driveUpload->setUpload();


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

   <a href="index.php?list_files_and_folders=1">Listar todos os arquivos da pasta </a> <br><br><br>


    <form action="" method="POST" enctype="multipart/form-data">
       <input type="file" name="file" />

       <input type="submit" name="submit" />
    </form>

</body>
</html>

