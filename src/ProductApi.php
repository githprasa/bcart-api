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

    public function bulkaddProduct($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            if(is_array($params) && count($params) > 0) {
                $query = "INSERT INTO product (Product, Description, Detail_Description, Category,
                        Vendor,ERP_Item_Reference,IsActive,ContractNumber,ContractItemNumber,Deliverytime,Location,price) 
                        values (:product,:description,:detaildescription,:category,:vendor,:erp,:isactive,:contractnumber,
                        :contractitemnumber,:deliverytime,:location,:price)";
                $conn = $this->db->connect();            
                $stmt = $conn->prepare($query);
                foreach ($params as $products) {
                    $stmt->bindParam(':product', $products['product']);
                    $stmt->bindParam(':description', $products['description']);
                    $stmt->bindParam(':detaildescription', $products['detaildescription']);
                    $stmt->bindParam(':category', $products['category']);
                    $stmt->bindParam(':vendor', $products['vendor']);
                    $stmt->bindParam(':erp', $products['erp']);
                    $stmt->bindParam(':isactive', $products['isactive']);
                    $stmt->bindParam(':contractnumber', $products['contractnumber']);
                    $stmt->bindParam(':contractitemnumber', $products['contractitemnumber']);
                    $stmt->bindParam(':deliverytime', $products['deliverytime']);
                    $stmt->bindParam(':location', $products['location']);
                    $stmt->bindParam(':price', $products['price']);
                    $stmt->execute();
                }
                $response['status']=true;
                $response['data']='Data inserted successfully';
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

    public function deleteProduct($productId) {
        $response = [];
        $response['status'] = false;
        try {
            $query = "DELETE FROM product WHERE id = :productId";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Product deleted successfully';
            } else {
                $response['message'] = 'No product found or already deleted';
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function updateProduct($productDetails) {
        $response = [];
        $response['status'] = false;
        try {
            $query = "UPDATE product SET Product = :product, Description = :description, Detail_Description = :detailDescription, 
                      Category = :category, Vendor = :vendor, ERP_Item_Reference = :erpItemReference, IsActive = :isActive, 
                      ContractNumber = :contractNumber, ContractItemNumber = :contractItemNumber, Deliverytime = :deliveryTime, 
                      Location = :location, Price = :price WHERE id = :id";
    
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            // Bind parameters from the array
            $stmt->bindParam(':id', $productDetails['id']);
            $stmt->bindParam(':product', $productDetails['product']);
            $stmt->bindParam(':description', $productDetails['description']);
            $stmt->bindParam(':detailDescription', $productDetails['detailDescription']);
            $stmt->bindParam(':category', $productDetails['category']);
            $stmt->bindParam(':vendor', $productDetails['vendor']);
            $stmt->bindParam(':erpItemReference', $productDetails['erp']);
            $stmt->bindParam(':isActive', $productDetails['isActive']);
            $stmt->bindParam(':contractNumber', $productDetails['contractNumber']);
            $stmt->bindParam(':contractItemNumber', $productDetails['contractItemNumber']);
            $stmt->bindParam(':deliveryTime', $productDetails['deliveryTime']);
            $stmt->bindParam(':location', $productDetails['location']);
            $stmt->bindParam(':price', $productDetails['price']);
    
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Product updated successfully';
            } else {
                $response['message'] = 'No changes made or product not found';
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            $response['file'] = $e->getFile();
            $response['line number'] = $e->getLine();
            $response['logResult'] = -1;
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
    

}
