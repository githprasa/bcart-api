<?php
namespace App;

class SettingsApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getSettingsList(){

        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from settings";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $row_result = $stmt->fetchAll();
            if(count($row_result) >0) {
                $response['status']=true;
                $response['data']=$row_result;           
                
            }
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
    
    public function getSettingsdetails($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from settings 
                      where SettingId = :id";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row_result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if(count($row_result) >0) {
                $response['status']=true;
                $response['data']=$row_result;           
                
            }
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

    public function addSetting($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "INSERT INTO settings (AppName, ApiUrl,ApiKey,ApiSecret,AdditionalFields) 
                      values (:appname,:apiurl,:apikey,:apisecret,:additionalfields)";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':appname', $params['appname']);
            $stmt->bindParam(':apiurl', $params['apiurl']);
            $stmt->bindParam(':apikey', $params['apikey']);
            $stmt->bindParam(':apisecret', $params['apisecret']);
            $stmt->bindParam(':additionalfields', $params['additionalfields']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New Settings added successfully';
            }
            
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

    public function updateSetting($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "UPDATE settings SET AppName = :appname, ApiUrl = :apiurl,ApiKey = :apikey,
            ApiSecret =:apisecret,AdditionalFields = :additionalfields WHERE SettingId =:id ";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':appname', $params['appname']);
            $stmt->bindParam(':apiurl', $params['apiurl']);
            $stmt->bindParam(':apikey', $params['apikey']);
            $stmt->bindParam(':apisecret', $params['apisecret']);
            $stmt->bindParam(':additionalfields', $params['additionalfields']);
            $stmt->bindParam(':id', $params['id']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Updated Setting successfully';
            }
            
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

    public function deleteSetting($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "DELETE FROM settings WHERE SettingId= :id LIMIT 1";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Deleted successfully';
            }
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
}