<?php

require_once 'vendor/autoload.php';
require_once "./random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=dicodingwebap;AccountKey=H7gAdwkRBprKnCT74avSaoBQs+QbQJGGni0VSGSi7K4CTdcBjOPPQaAE/gzTNtcPgzn5wdUV083OeOjH64M2gA==;";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "blockblobsjlgpsa";
//$fileToUpload = "HelloWorld.txt";
if (isset($_POST["submit"])) {
    
   
    try {
        // Create container.
		$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
        
        $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
        //Upload blob
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
		header("Location: index.php");
        
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
} 
?>
<html>
<head>
    <title>Azure 2 Submission</title>
  </head>
  <body>
	 
	<div >
		<form class="d-flex justify-content-lefr" action="index.php" method="post" enctype="multipart/form-data">
			<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
			<input type="submit" name="submit" value="Upload">
		</form>
	</div>
	<br>
	<br>
	<h4>Uploaded Files</h4>
	<table>
	<thead>
	<tr>
		<th>Name</th>
		<th>URL</th>
		<th>Action</th>
		</tr>
		</thead>
		<tbody>
			<?php
				$listBlobsOptions = new ListBlobsOptions();
					$listBlobsOptions->setPrefix("");
					
					do {
				
						$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
						foreach ($result->getBlobs() as $blob)
						{
						
							?>
							<tr>
								<td><?php echo $blob->getName() ?></td>
								<td><?php echo $blob->getUrl() ?></td>
								<td>
									<form action="mycomputervision.php" method="POST">
										<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
										<input type="submit" name="submit" value="Do Analyze" >
									</form>
								</td>
							</tr>
							<?php
						}
						$listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
					
					 $blob = $blobClient->getBlob($containerName, $fileToUpload);
					fpassthru($blob->getContentStream());
					?>
				</tbody>
			</table>
	</div>	
  </body>
</html>