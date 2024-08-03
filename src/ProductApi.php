<?php
namespace App;

class ProductApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function productResult($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT P.id, P.Product , P.Description , P.Detail_Description, P.ERP_Item_Reference, P.IsActive, C.Category, V.vendor,
                      P.ContractNumber, P.ContractItemNumber, P.Deliverytime, P.Category as CategoryId,P.Vendor as VendorId,P.Location as LocationId,
                      L.LocationName, P.price, P.imagefiles,ci.Quantity from product P 
                      left join category C on C.id = P.Category
                      left join vendor V on V.id = P.Vendor
                      left join locations L on L.Id = P.Location
                      left join cartitems ci on ci.ProductId = P.id and UserId=:id";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row_result = $stmt->fetchAll();
            if (count($row_result) >0) {
                $response['status'] = true;
                $response['data'] = $row_result;
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error : '.$e->getMessage();
            $response['file'] = $e->getFile();
            $response['line number'] = $e->getLine();
            $response['logResult'] = -1;            
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function productlist() {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT P.id, P.Product , P.Description , P.Detail_Description, P.ERP_Item_Reference, P.IsActive, C.Category, V.vendor,
                      P.ContractNumber, P.ContractItemNumber, P.Deliverytime, P.Category as CategoryId,P.Vendor as VendorId,P.Location as LocationId,
                      L.LocationName, P.price, P.imagefiles from product P 
                      left join category C on C.id = P.Category
                      left join vendor V on V.id = P.Vendor
                      left join locations L on L.Id = P.Location";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $row_result = $stmt->fetchAll();
            if (count($row_result) >0) {
                $response['status'] = true;
                $response['data'] = $row_result;
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error : '.$e->getMessage();
            $response['file'] = $e->getFile();
            $response['line number'] = $e->getLine();
            $response['logResult'] = -1;            
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
            $query = "SELECT P.id,P.Product,P.Description,P.Detail_Description,C.Category as Category,V.Vendor as Vendor,P.Vendor as Vendorid,
                    P.ERP_Item_Reference,P.IsActive,P.ContractNumber,
                    P.ContractItemNumber,P.Deliverytime,L.LocationName as Location,
                    P.price,P.imagefiles from product P left join category C on C.id = P.Category
                      left join vendor V on V.id = P.Vendor
                      left join locations L on L.Id = P.Location where P.id = :id";

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

    public function addProduct($params) {
        $response=[];
        $response['status'] = false;
        $response['data'] = '';
        try {

            $conn = $this->db->connect();
            $locationQuery = "SELECT Id FROM locations WHERE LocationName = :location";
            $locationStmt = $conn->prepare($locationQuery);
            $locationStmt->bindParam(':location', $params['Location']);
            $locationStmt->execute();
            $location = $locationStmt->fetch(\PDO::FETCH_ASSOC);
            $location = $location['Id'] ?? '0';

            $vendorQuery = "SELECT id FROM vendor WHERE Vendor = :vendor";
            $vendorStmt = $conn->prepare($vendorQuery);
            $vendorStmt->bindParam(':vendor', $params['Vendor']);
            $vendorStmt->execute();
            $vendor = $vendorStmt->fetch(\PDO::FETCH_ASSOC);
            $vendorId = $vendor['id'] ?? '0'; 

            $categoryQuery = "SELECT id FROM category WHERE Category = :category";
            $categoryStmt = $conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category', $params['Category']);
            $categoryStmt->execute();
            $category = $categoryStmt->fetch(\PDO::FETCH_ASSOC);
            $categoryId = $category['id'] ?? '0';

            $query = "INSERT INTO product (Product, Description, Detail_Description, Category,
                      Vendor,ERP_Item_Reference,IsActive,ContractNumber,ContractItemNumber,Deliverytime,Location,price) 
                      values (:product,:description,:detaildescription,:category,:vendor,:erp,:isactive,:contractnumber,
                      :contractitemnumber,:deliverytime,:location,:price)";

                        
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':product', $params['Product']);
            $stmt->bindParam(':description', $params['Description']);
            $stmt->bindParam(':detaildescription', $params['Detail_Description']);
            $stmt->bindParam(':category', $categoryId);
            $stmt->bindParam(':vendor', $vendorId);
            $stmt->bindParam(':erp', $params['ERP_Item_Reference']);
            $stmt->bindParam(':isactive', $params['IsActive']);
            $stmt->bindParam(':contractnumber', $params['ContractNumber']);
            $stmt->bindParam(':contractitemnumber', $params['ContractItemNumber']);
            $stmt->bindParam(':deliverytime', $params['Deliverytime']);
            $stmt->bindParam(':location', $location);
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

    public function bulkaddProduct($params) {
        $response=[];
        $response['status'] = false;
        $response['data'] = '';
        try {
            if (is_array($params) && count($params) > 0) {
                $query = "INSERT INTO product (Product, Description, Detail_Description, Category,
                        Vendor,ERP_Item_Reference,IsActive,ContractNumber,ContractItemNumber,Deliverytime,Location,price) 
                        values (:product,:description,:detaildescription,:category,:vendor,:erp,:isactive,:contractnumber,
                        :contractitemnumber,:deliverytime,:location,:price)";
                $conn = $this->db->connect();            
                $stmt = $conn->prepare($query);
                foreach ($params as $products) {
                    $products['location'] = $this->getOrInsert($conn, 'locations', 'LocationName', ($products['location'] ?? ''));
                    $products['vendor'] = $this->getOrInsert($conn, 'vendor', 'Vendor', ($products['vendor'] ?? ''));
                    $products['category'] = $this->getOrInsert($conn, 'category', 'Category', ($products['category'] ?? ''));
                    $response['datdda'] = $products;
                    $stmt->bindParam(':product', $products['product']);
                    $stmt->bindParam(':description', $products['description']);
                    $stmt->bindParam(':detaildescription', $products['detail_description']);
                    $stmt->bindParam(':category', $products['category']);
                    $stmt->bindParam(':vendor', $products['vendor']);
                    $stmt->bindParam(':erp', $products['erp_item_reference']);
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

    public function getOrInsert($conn, $table, $column, $value) {
        try {
            if(trim($value) == '') {
                return '';
            }
            $response=[];
            $query = "SELECT id FROM $table WHERE $column = :value";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            if ($result) {
                return $result['id'] ?? '0';
            } else {
                $insertQuery = "INSERT INTO $table ($column) VALUES (:value)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bindParam(':value', $value);
                $insertStmt->execute();
                return $conn->lastInsertId();
            }
        } catch (\Exception $e) {
            $response['message']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;            
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
            $conn = $this->db->connect();
            $locationQuery = "SELECT Id FROM locations WHERE LocationName = :location";
            $locationStmt = $conn->prepare($locationQuery);
            $locationStmt->bindParam(':location', $productDetails['Location']);
            $locationStmt->execute();
            $location = $locationStmt->fetch(\PDO::FETCH_ASSOC);
            $location=$location['Id'] ?? '0';

            $vendorQuery = "SELECT id FROM vendor WHERE Vendor = :vendor";
            $vendorStmt = $conn->prepare($vendorQuery);
            $vendorStmt->bindParam(':vendor', $productDetails['Vendor']);
            $vendorStmt->execute();
            $vendor = $vendorStmt->fetch(\PDO::FETCH_ASSOC);
            $vendorId = $vendor['id'] ?? '0';

            $categoryQuery = "SELECT id FROM category WHERE Category = :category";
            $categoryStmt = $conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category', $productDetails['Category']);
            $categoryStmt->execute();
            $category = $categoryStmt->fetch(\PDO::FETCH_ASSOC);
            $categoryId = $category['id'] ?? '0';

            $query = "UPDATE product SET Product = :product, `Description` = :descriptions, Detail_Description = :detaildescription, 
                      Category = :category, Vendor = :vendor, ERP_Item_Reference = :erp, IsActive = :isactive, 
                      ContractNumber = :contractnumber, ContractItemNumber = :contractitemnumber, Deliverytime = :deliverytime, 
                      `Location` = :location, Price = :price WHERE id = :id";
            
            $stmt = $conn->prepare($query);
            
            $stmt->bindParam(':id', $productDetails['id']);          
            $stmt->bindParam(':product', $productDetails['Product']);
            $stmt->bindParam(':descriptions', $productDetails['Description']);
            $stmt->bindParam(':detaildescription', $productDetails['Detail_Description']);
            $stmt->bindParam(':category', $categoryId);
            $stmt->bindParam(':vendor', $vendorId);
            $stmt->bindParam(':erp', $productDetails['ERP_Item_Reference']);
            $stmt->bindParam(':isactive', $productDetails['IsActive']);
            $stmt->bindParam(':contractnumber', $productDetails['ContractNumber']);
            $stmt->bindParam(':contractitemnumber', $productDetails['ContractItemNumber']);
            $stmt->bindParam(':deliverytime', $productDetails['Deliverytime']);
            $stmt->bindParam(':location', $location);
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
