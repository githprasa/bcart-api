<?php
namespace App;

class ProductApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function productResult() {
        $response=[];
        $response['status']=false;
        $response['data'][]='';
        try {
            $query = "SELECT P.id, P.Product , P.Description , P.Detail_Description, P.ERP_Item_Reference, P.IsActive, C.Category, V.vendor,
                      P.ContractNumber, P.ContractItemNumber, P.Deliverytime, L.LocationName, P.price from product P 
                      inner join category C on C.id = P.Category
                      inner join vendor V on V.id = P.Vendor
                      inner join locations L on L.Id = P.Location";

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

    public function addProduct($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "INSERT INTO product (Product, Description, Detail_Description, Category,
                      Vendor,ERP_Item_Reference,IsActive,ContractNumber,ContractItemNumber,Deliverytime,Location,price) 
                      values (:product,:description,:detaildescription,:category,:vendor,:erp,:isactive,:contractnumber,
                      :contractitemnumber,:deliverytime,:location,:price)";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':product', $params['product']);
            $stmt->bindParam(':description', $params['description']);
            $stmt->bindParam(':detaildescription', $params['detaildescription']);
            $stmt->bindParam(':category', $params['category']);
            $stmt->bindParam(':vendor', $params['vendor']);
            $stmt->bindParam(':erp', $params['erp']);
            $stmt->bindParam(':isactive', $params['isactive']);
            $stmt->bindParam(':contractnumber', $params['contractnumber']);
            $stmt->bindParam(':contractitemnumber', $params['contractitemnumber']);
            $stmt->bindParam(':deliverytime', $params['deliverytime']);
            $stmt->bindParam(':location', $params['location']);
            $stmt->bindParam(':price', $params['price']);
            $stmt->execute();
            
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
