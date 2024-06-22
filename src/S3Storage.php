<?php
namespace App;
use Aws\S3\S3Client;
class S3Storage {
    private $db;
    private $client;
    private $bucketName;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->client = new S3Client([
            'version' => 'latest',
            'region'  => 'ap-south-1',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);
        $this->bucketName ='abovsoftfiles';
    }

    public function imagesave($params=null) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            //$file = $_FILES['file']['tmp_name'];
            //$fileName = 'test/' . $_FILES['file']['name'];

            $file = __DIR__ . '/dummy-file.txt';
            $fileName = 'dummy-file.txt';
            if (!file_exists($file)) {
                file_put_contents($file, 'This is a dummy file for testing S3 upload.');
            }
            $result = $this->client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => 'test/'.$fileName,
                'SourceFile' => $file,
            ]);
            $response['status']=true;
            $response['message']='Image uploaded succesfully';
            $response['imageUrl']=$result->get('ObjectURL');
        } catch (\Exception $e) {
            $response['message']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;            
        } finally {
            $this->db->close();
            return $response;
        }
    }


    public function getImageFiles($id=null) {
        $response = [];
        $response['status']=false;
        $response['data']='';
        try {
            $pid='temp';
            if(!empty($id)) $pid=$id;
            $objects = $this->client->listObjectsV2([
                'Bucket' => $this->bucketName,
                'Prefix' => 'product/'.$pid
            ]);
            $files = [];
            if (is_array($objects['Contents']) && count($objects['Contents']) >0) {
                foreach ($objects['Contents'] as $object) {
                    $files[] = [
                        'key' => $object['Key'],
                        'url' => $this->client->getObjectUrl($this->bucketName, $object['Key']),
                    ];
                }
            }
            $response['status']=true;
            $response['data']=$files;
            $filesJson = json_encode($files);
            $response['filesJson']=$filesJson;
            $conn = $this->db->connect();
            $sql = "UPDATE product SET imagefiles = :imagefiles WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['imagefiles' => $filesJson, 'id' => $pid]);
        } catch (\Exception $e) {
            $response['message']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;            
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function deleteImage($params=null) {
        $response = [];
        $response['status'] = false;
        $response['data'] = '';
        try {
            $fileName=$params['fileName'] ?? '';
            $result = $this->client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $fileName,
            ]);
            $response['status'] = true;
            $response['message'] = 'Image deleted successfully';
        } catch (\Exception $e) {
            $response['message'] = 'Error : ' . $e->getMessage();
            $response['file'] = $e->getFile();
            $response['line number'] = $e->getLine();
            $response['logResult'] = -1;
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function getPresignedUrl($params=null) {
        $response = [];
        $response['status'] = false;
        try {
            $fileName = $params['fileName'] ?? 'test.jpg';
            $productid = $params['productid'] ?? 'temp';
            $fileKey = 'product/'.$productid.'/'.$fileName; 
            $response['fileKey'] = $fileKey;
            // Setting up the presigned request options
            $cmd = $this->client->getCommand('PutObject', [
                'Bucket' => $this->bucketName,
                'Key'    => $fileKey,
                'ContentType' => 'image/jpeg'
            ]);
            $expiry = "+20 minutes";
            $request = $this->client->createPresignedRequest($cmd, $expiry);
            $presignedUrl = (string) $request->getUri();
            $response['status'] = true;
            $response['presignedUrl'] = $presignedUrl;
            $response['fields'] = ['key'=>$fileKey];
        } catch (\Exception $e) {
            $response['message'] = 'Error : ' . $e->getMessage();
            $response['file'] = $e->getFile();
            $response['line number'] = $e->getLine();
            $response['logResult'] = -1;
        } finally {
            $this->db->close();
            return $response;
        }
    }

}
