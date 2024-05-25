<?php
namespace App;

class OrdersApi {

    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function createOrder($param) {
        $response = ['status' => false];
        try {
            $query = "INSERT INTO orders (UserId, OrderDate, TotalAmount, ApiCallStatus) VALUES (:userId, :orderDate, :totalAmount, :apiCallStatus)";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $param['apiCallStatus'] = (isset($param['apiCallStatus']) && !empty($param['apiCallStatus'])) ? $param['apiCallStatus'] : 'Pending';
            $stmt->bindParam(':userId', $param['userId']);
            $stmt->bindParam(':orderDate', $param['orderDate']);
            $stmt->bindParam(':totalAmount', $param['totalAmount']);
            $stmt->bindParam(':apiCallStatus', $param['apiCallStatus']);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Order created successfully.';
            } else {
                $response['message'] = 'Failed to add item to cart.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
    // public function deleteOrder($param) {
    //     $response = ['status' => false];
    //     try {
    //         $query = "DELETE FROM orders WHERE OrderId = :orderId";
    //         $conn = $this->db->connect();
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':orderId', $param['orderId']);
    //         $stmt->execute();
    
    //         if ($stmt->rowCount() > 0) {
    //             $response['status'] = true;
    //             $response['message'] = 'Order deleted successfully.';
    //         } else {
    //             $response['message'] = 'No changes made or order item not found.';
    //         }
    //     } catch (Exception $e) {
    //         $response['message'] = 'Error: ' . $e->getMessage();
    //     } finally {
    //         $this->db->close();
    //         return $response;
    //     }
    // }
    
    // public function updateOrder($param) {
    //     $response = ['status' => false];
    //     try {
    //         $query = "UPDATE orders SET UserId = :userId, OrderDate = :orderDate, TotalAmount = :totalAmount, IsCompleted = :isCompleted, ApiCallStatus = :apiCallStatus WHERE OrderId = :orderId";
    //         $conn = $this->db->connect();
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':orderId', $param['orderId']);
    //         $stmt->bindParam(':userId', $param['userId']);
    //         $stmt->bindParam(':orderDate', $param['orderDate']);
    //         $stmt->bindParam(':totalAmount', $param['totalAmount']);
    //         $stmt->bindParam(':isCompleted', $param['isCompleted']);
    //         $stmt->bindParam(':apiCallStatus', $param['apiCallStatus']);
    //         $stmt->execute();
    //         if ($stmt->rowCount() > 0) {
    //             $response['status'] = true;
    //             $response['message'] = 'Order updated successfully.';
    //         } else {
    //             $response['message'] = 'No changes made or order item not found.';
    //         }
    //     } catch (Exception $e) {
    //         $response['message'] = 'Error: ' . $e->getMessage();
    //     } finally {
    //         $this->db->close();
    //         return $response;
    //     }
    // }
    
    public function createOrderItem($param) {
        $response = ['status' => false];
        try {
            $query = "SELECT * FROM cartitems WHERE OrderId = :orderId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':orderId', $param['orderId']);
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cartItems as $cartItem) {
                $query = "INSERT INTO orderitems (OrderId, ProductId, Quantity, Price, VendorId, LocationId) VALUES (:orderId, :productId, :quantity, :price, :vendorId, :locationId)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':orderId', $cartItem['OrderId']);
                $stmt->bindParam(':productId', $cartItem['ProductId']);
                $stmt->bindParam(':quantity', $cartItem['Quantity']);
                $stmt->bindParam(':price', $cartItem['Price']);
                $stmt->bindParam(':vendorId', $cartItem['VendorId']);
                $stmt->bindParam(':locationId', $cartItem['LocationId']);
                $stmt->execute();
            }

            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'Order item added successfully.';
            } else {
                $response['message'] = 'Failed to add item to cart.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
    // public function deleteOrderItem($param) {
    //     $response = ['status' => false];
    //     try {
    //         $query = "DELETE FROM orderitems WHERE OrderItemId = :orderItemId";
    //         $conn = $this->db->connect();
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':orderItemId', $param['orderItemId']);
    //         $stmt->execute();
    
    //         if ($stmt->rowCount() > 0) {
    //             $response['status'] = true;
    //             $response['message'] = 'Order item deleted successfully.';
    //         } else {
    //             $response['message'] = 'No changes made or order item not found.';
    //         }
    //     } catch (Exception $e) {
    //         $response['message'] = 'Error: ' . $e->getMessage();
    //     } finally {
    //         $this->db->close();
    //         return $response;
    //     }
    // }

    // public function updateOrderItem($param) {
    //     $response = ['status' => false];
    //     try {
    //         $query = "UPDATE orderitems SET OrderId = :orderId, ProductId = :productId, Quantity = :quantity, Price = :price, VendorId = :vendorId, LocationId = :locationId WHERE OrderItemId = :orderItemId";
    //         $conn = $this->db->connect();
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':orderItemId', $param['orderItemId']);
    //         $stmt->bindParam(':orderId', $param['orderId']);
    //         $stmt->bindParam(':productId', $param['productId']);
    //         $stmt->bindParam(':quantity', $param['quantity']);
    //         $stmt->bindParam(':price', $param['price']);
    //         $stmt->bindParam(':vendorId', $param['vendorId']);
    //         $stmt->bindParam(':locationId', $param['locationId']);
    //         $stmt->execute();
    
    //         if ($stmt->rowCount() > 0) {
    //             $response['status'] = true;
    //             $response['message'] = 'Order item updated successfully.';
    //         } else {
    //             $response['message'] = 'No changes made or order item not found.';
    //         }
    //     } catch (Exception $e) {
    //         $response['message'] = 'Error: ' . $e->getMessage();
    //     } finally {
    //         $this->db->close();
    //         return $response;
    //     }
    // }
}
