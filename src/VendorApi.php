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
        $response['data'][]='';
        try {
            $query = "SELECT v.id, v.Vendor, v.Description, v.ERP_Reference , l.LocationName from vendor v 
                        inner join locations l on l.Id = v.Location";

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

    
}
