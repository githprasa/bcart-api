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
            $query = "INSERT INTO cartitems (UserId, ProductId, Quantity, VendorId, Location) VALUES (:userId, :productId, :quantity, :vendorId, :locationId)";
            $conn = $this->db->connect();            
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
            $stmt->bindParam(':id', $params['id']);
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
    
}
