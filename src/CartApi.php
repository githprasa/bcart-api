<?php
namespace App;

class CartApi{

    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function insertCartItem($params) {
        $response = ['status' => false];
        try {
            $conn = $this->db->connect();     
            $checkQuery = "SELECT Quantity FROM cartitems WHERE UserId = :userId AND ProductId = :productId";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':userId', $params['userId']);
            $checkStmt->bindParam(':productId', $params['productId']);
            $checkStmt->execute();
            $result = $checkStmt->fetch(\PDO::FETCH_ASSOC);
            if ($result) {
                $newQuantity = $result['Quantity'] + $params['quantity'];
                $updateQuery = "
                    UPDATE cartitems 
                    SET Quantity = :quantity, VendorId = :vendorId, Location = :locationId 
                    WHERE UserId = :userId AND ProductId = :productId
                ";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':quantity', $newQuantity);
                $updateStmt->bindParam(':vendorId', $params['vendorId']);
                $updateStmt->bindParam(':locationId', $params['locationId']);
                $updateStmt->bindParam(':userId', $params['userId']);
                $updateStmt->bindParam(':productId', $params['productId']);
                $updateStmt->execute();

                if ($updateStmt->rowCount() > 0) {
                    $response['status'] = true;
                    $response['message'] = 'Item quantity updated in cart successfully.';
                }
            } else {
                $query = "INSERT INTO cartitems (UserId, ProductId, Quantity, VendorId, Location) VALUES (:userId, :productId, :quantity, :vendorId, :locationId)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':userId', $params['userId']);
                $stmt->bindParam(':productId', $params['productId']);
                $stmt->bindParam(':quantity', $params['quantity']);
                $stmt->bindParam(':vendorId', $params['vendorId']);
                $stmt->bindParam(':locationId', $params['locationId']);
                $stmt->execute();
        
                if ($stmt->rowCount() > 0) {
                    $response['status'] = true;
                    $response['message'] = 'Item added to cart successfully.';
                }else {
                    $response['message'] = 'Failed to add item to cart.';
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function getCartItems($userId) {
        $response = ['status' => false, 'data' => []];
        try {
            $query = "SELECT c.id as cartItemId,c.UserId,c.ProductId,c.Quantity,c.VendorId as vendor,c.Location as location,
            p.Product as name,p.price as price FROM cartitems c JOIN  product p ON c.ProductId = p.id WHERE c.UserId=:userId";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $items = $stmt->fetchAll();
    
            if ($items) {
                $response['status'] = true;
                $response['data'] = $items;
            } else {
                $response['message'] = 'No items found in cart.';
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
    public function deleteCartItem($params) {
        $response = ['status' => false];
        try {
            $query = "DELETE FROM cartitems WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $params['cartid']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Item removed from cart successfully.';
            } else {
                $response['message'] = 'No changes made or cart item not found.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
    public function updateCartItem($params) {
        $response = ['status' => false];
        try {
            $query = "UPDATE cartitems SET UserId = :userId, ProductId = :productId, Quantity = :quantity, VendorId = :vendorId, Location = :locationId WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $params['id']);
            $stmt->bindParam(':userId', $params['userId']);
            $stmt->bindParam(':productId', $params['productId']);
            $stmt->bindParam(':quantity', $params['quantity']);
            $stmt->bindParam(':vendorId', $params['vendorId']);
            $stmt->bindParam(':locationId', $params['locationId']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Cart item updated successfully.';
            }else {
                $response['message'] = 'No changes made or cart item not found.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }


    public function updateCartItemQuantity($params) {
        $response = ['status' => false];
        try {
            $query = "UPDATE cartitems SET Quantity = :quantity WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':quantity', $params['quantity']);
            $stmt->bindParam(':id', $params['id']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Cart item updated successfully.';
            } else {
                $response['message'] = 'No changes made or cart item not found.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function updateCartItemVendorId($params) {
        $response = ['status' => false];
        try {
            $query = "UPDATE cartitems SET VendorId = :vendorId WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':vendorId', $params['vendorId']);
            $stmt->bindParam(':id', $params['id']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Cart item updated successfully.';
            } else {
                $response['message'] = 'No changes made or cart item not found.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function updateCartItemLocation($params) {
        $response = ['status' => false];
        try {
            $query = "UPDATE cartitems SET Location = :locationId WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':locationId', $params['locationId']);
            $stmt->bindParam(':id', $params['id']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Cart item updated successfully.';
            }else {
                $response['message'] = 'No changes made or cart item not found.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function checkCartItem($params) {
        $response = ['status' => false, 'exists' => false];
        try {
            $userId = $params['userId'] ?? '';
            $productId = $params['productId'] ?? '';
            $query = "SELECT COUNT(*) as count FROM cartitems WHERE UserId = :userId AND ProductId = :productId";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                $response['status'] = true;
                $response['exists'] = true;
            } else {
                $response['status'] = true;
                $response['exists'] = false;
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
}
