<?php
namespace App;

class VendorApi{

    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getVendorList(){

        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT v.id, v.Vendor, v.Description, v.ERP_Reference , l.LocationName from vendor v 
                        left join locations l on l.Id = v.Location";
            // $query = "SELECT * from vendor";

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
    
    public function getVendordetails($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from vendor 
                      where id = :id";

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

    public function addVendor($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $conn = $this->db->connect();  
            $locationQuery = "SELECT Id FROM locations WHERE LocationName = :location";
            $locationStmt = $conn->prepare($locationQuery);
            $locationStmt->bindParam(':location', $params['location']);
            $locationStmt->execute();
            $location = $locationStmt->fetch(\PDO::FETCH_ASSOC);
            $location=$location['Id'] ?? '0';
            $query = "INSERT INTO vendor (Vendor, Description,Location,ERP_Reference) 
                      values (:vendor,:description,:location,:erp_ref)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':vendor', $params['vendor']);
            $stmt->bindParam(':description', $params['description']);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':erp_ref', $params['erp_ref']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New Vendor added successfully';
            }
            
        } catch (\Exception $e) {
            echo "error ";
            $response['message']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;            
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function updateVendor($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $conn = $this->db->connect();  
            $locationQuery = "SELECT Id FROM locations WHERE LocationName = :location";
            $locationStmt = $conn->prepare($locationQuery);
            $locationStmt->bindParam(':location', $params['location']);
            $locationStmt->execute();
            $location = $locationStmt->fetch(\PDO::FETCH_ASSOC);
            $location=$location['Id'] ?? '0';
            $query = "UPDATE vendor SET Vendor=:vendor, Description= :description, Location = :location,ERP_Reference = :erp_ref WHERE id =:id ";
         
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':vendor', $params['vendor']);
            $stmt->bindParam(':description', $params['description']);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':erp_ref', $params['erp_ref']);
            $stmt->bindParam(':id', $params['id']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Updated vendor successfully';
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

    public function deleteVendor($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "DELETE FROM vendor WHERE id= :id LIMIT 1";

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
