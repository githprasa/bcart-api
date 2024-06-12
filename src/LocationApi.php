<?php
namespace App;

class LocationApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getLocationList(){

        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from locations";

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
    
    public function getLocationdetails($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from locations 
                      where Id = :id";

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

    public function addLocation($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "INSERT INTO locations (LocationName, Area,City,StateProvince,Country,ZipCode) 
                      values (:location,:area,:city,:state,:country,:zip)";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':location', $params['location']);
            $stmt->bindParam(':area', $params['area']);
            $stmt->bindParam(':city', $params['city']);
            $stmt->bindParam(':state', $params['state']);
            $stmt->bindParam(':country', $params['country']);
            $stmt->bindParam(':zip', $params['zip']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New Location added successfully';
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

    public function updateLocation($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "UPDATE locations SET LocationName=:location, Area= :area, City = :city,StateProvince = :state,
            Country = :country,ZipCode = :zip WHERE Id =:id ";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':location', $params['location']);
            $stmt->bindParam(':area', $params['area']);
            $stmt->bindParam(':city', $params['city']);
            $stmt->bindParam(':state', $params['state']);
            $stmt->bindParam(':country', $params['country']);
            $stmt->bindParam(':zip', $params['zip']);
            $stmt->bindParam(':id', $params['id']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Updated Location successfully';
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

    public function deleteLocation($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "DELETE FROM locations WHERE Id= :id LIMIT 1";

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