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
        $response['data']='';
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

    public function getProductdetails($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from product P 
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
            $stmt->bindParam(':product', $params['Product']);
            $stmt->bindParam(':description', $params['Description']);
            $stmt->bindParam(':detaildescription', $params['Detail_Description']);
            $stmt->bindParam(':category', $params['Category']);
            $stmt->bindParam(':vendor', $params['Vendor']);
            $stmt->bindParam(':erp', $params['ERP_Item_Reference']);
            $stmt->bindParam(':isactive', $params['IsActive']);
            $stmt->bindParam(':contractnumber', $params['ContractNumber']);
            $stmt->bindParam(':contractitemnumber', $params['ContractItemNumber']);
            $stmt->bindParam(':deliverytime', $params['Deliverytime']);
            $stmt->bindParam(':location', $params['Location']);
            $stmt->bindParam(':price', $params['price']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New Product added successfully';
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
                    $stmt->bindParam(':product', $products['Product']);
                    $stmt->bindParam(':description', $products['Description']);
                    $stmt->bindParam(':detaildescription', $products['Detail_Description']);
                    $stmt->bindParam(':category', $products['Category']);
                    $stmt->bindParam(':vendor', $products['Vendor']);
                    $stmt->bindParam(':erp', $products['ERP_Item_Reference']);
                    $stmt->bindParam(':isactive', $products['IsActive']);
                    $stmt->bindParam(':contractnumber', $products['ContractNumber']);
                    $stmt->bindParam(':contractitemnumber', $products['ContractItemNumber']);
                    $stmt->bindParam(':deliverytime', $products['Deliverytime']);
                    $stmt->bindParam(':location', $products['Location']);
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
            $stmt->bindParam(':product', $productDetails['Product']);
            $stmt->bindParam(':description', $productDetails['Description']);
            $stmt->bindParam(':detaildescription', $productDetails['Detail_Description']);
            $stmt->bindParam(':category', $productDetails['Category']);
            $stmt->bindParam(':vendor', $productDetails['Vendor']);
            $stmt->bindParam(':erp', $productDetails['ERP_Item_Reference']);
            $stmt->bindParam(':isactive', $productDetails['IsActive']);
            $stmt->bindParam(':contractnumber', $productDetails['ContractNumber']);
            $stmt->bindParam(':contractitemnumber', $productDetails['ContractItemNumber']);
            $stmt->bindParam(':deliverytime', $productDetails['Deliverytime']);
            $stmt->bindParam(':location', $productDetails['Location']);
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
