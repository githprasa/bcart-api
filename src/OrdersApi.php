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
            $query = "INSERT INTO orders (UserId,OrderDate, TotalAmount, ApiCallStatus) VALUES (:UserId,:orderDate, :totalAmount, :apiCallStatus)";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $param['apiCallStatus'] = (isset($param['apiCallStatus']) && !empty($param['apiCallStatus']))? $param['apiCallStatus'] : 'Pending';
            $stmt->bindParam(':orderDate', $param['orderDate']);
            $stmt->bindParam(':totalAmount', $param['totalAmount']);
            $stmt->bindParam(':apiCallStatus', $param['apiCallStatus']);
            $stmt->bindParam(':UserId', $param['UserId']);
            $stmt->execute();
            $lastInsertId = $conn->lastInsertId();
            if ($lastInsertId) {
                $query = "SELECT * FROM cartitems WHERE UserId = :UserId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':UserId', $param['UserId']);
                $stmt->execute();
                $cartItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (count($cartItems) > 0) {
                    foreach ($cartItems as $cartItem) {
                        $ProductId = $cartItem['ProductId'] ?? 0;
                        $VendorId = $cartItem['VendorId'] ?? 0;
                        $LocationId = $cartItem['Location'] ?? 0;
        
                        $query = "SELECT * FROM product WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $ProductId);
                        $stmt->execute();
                        $productItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                       
                        $Price = $productItems[0]['price']? ($productItems[0]['price'] * $cartItem['Quantity']) : 0;
                        $response['Price'] = $Price;
                        $jsonProductItems = count($productItems) > 0 ? json_encode($productItems) : json_encode([$ProductId]);
                        
                        
                        $query = "SELECT * FROM vendor WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $VendorId);
                        $stmt->execute();
                        $vendorItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $jsonvendorItems = count($vendorItems) > 0 ? json_encode($vendorItems) : json_encode([$VendorId]);
                        $response['jsonvendorItems'] = $jsonvendorItems;
                        $query = "SELECT * FROM locations WHERE Id = :Id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':Id', $LocationId);
                        $stmt->execute();
                        $locationItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $jsonlocationItems = count($locationItems) > 0 ? json_encode($locationItems) : json_encode([$LocationId]);
                        $response['jsonlocationItems'] = $jsonlocationItems;
                        $query = "INSERT INTO orderitems (OrderId, ProductId, Quantity, Price, VendorId, LocationId) VALUES (:orderId, :productId, :quantity, :price, :vendorId, :locationId)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':orderId', $lastInsertId);
                        $stmt->bindParam(':productId',$jsonProductItems);
                        $stmt->bindParam(':quantity', $cartItem['Quantity']);
                        $stmt->bindParam(':price', $Price);
                        $stmt->bindParam(':vendorId', $jsonvendorItems);
                        $stmt->bindParam(':locationId', $jsonlocationItems);
                        $stmt->execute();
                    }
                    $query = "DELETE FROM cartitems WHERE UserId = :UserId";
                    $conn = $this->db->connect();
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':UserId', $param['UserId']);
                    $stmt->execute();
                }
                $response['status'] = true;
                $response['message'] = 'Order created successfully.';
            } else {
                $response['message'] = 'Failed to add item to cart.';
            }
        } catch (Exception $e) {
           $response['message'] = 'Errorc: ' . $e->getMessage();
        } catch (\Exception $e) {
           $response['message'] = 'Error 777: ' . $e->getMessage();
           $response['line'] = 'Error 777: ' . $e->getLine();
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
            $query = "SELECT * FROM cartitems WHERE UserId = :UserId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':UserId', $param['UserId']);
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
