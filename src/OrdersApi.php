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
    
    public function createOrderItem($param) {
        $response = ['status' => false];
        try {
            $conn = $this->db->connect();
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
    
    public function getList($id) {
        $response = ['status' => false];
        try {
            $conn = $this->db->connect();
            // Fetch orders based on UserId
            $query = "SELECT *,DATE(orders.OrderDate) as odate,CONCAT(users.FirstName, ' ', users.LastName) as orderedBy FROM orders JOIN users ON orders.UserId = users.Id WHERE orders.UserId = :UserId";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':UserId', $id);
            $stmt->execute();
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
            if(count($orders)) {
                $orderIds = array_column($orders, 'OrderId');
                $orderIdsString = implode(',', array_map('intval', $orderIds));
    
                // Fetch order items based on OrderIds
                $query = "SELECT * FROM orderitems WHERE OrderId IN ($orderIdsString)";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $orderItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
                // Process and organize order items
                $orderDetails = [];
                foreach ($orderItems as $item) {
                    $productDetails = json_decode($item['ProductId'], true);
                    $vendorDetails = json_decode($item['VendorId'], true);
                    $locationDetails = json_decode($item['LocationId'], true);
    
                    if (!isset($orderDetails[$item['OrderId']])) {
                        $orderDetails[$item['OrderId']] = [];
                    }
    
                    $orderDetails[$item['OrderId']][] = [
                        'quantity' => $item['Quantity'],
                        'price' => $item['Price'],
                        'product' => $productDetails,
                        'vendor' => $vendorDetails,
                        'location' => $locationDetails
                    ];
                }

                $responseOrders = [];
                foreach ($orders as $order) {
                    $details = isset($orderDetails[$order['OrderId']]) ? $orderDetails[$order['OrderId']] : [];
                    $totalAmount = array_reduce($details, function ($sum, $item) {
                        return $sum + ($item['price'] * $item['quantity']);
                    }, 0);
                    $productDetailsString = implode(', ', array_map(function ($detail) {
                        return ($detail['product'][0]['Product'] ?? '') . ', Size: ' . ($detail['product'][0]['Description'] ?? '') . ' - Quantity: ' . ($detail['quantity'] ?? '');
                    }, $details));
    
                    $responseOrders[] = [
                        'id' => $order['OrderId'],
                        'orderedBy' => $order['orderedBy'] ?? '',
                        'orderDate' => $order['OrderDate'] ?? '',
                        'odate' => $order['odate'] ?? '',
                        'totalAmount' => $order['TotalAmount'] ? intval($order['TotalAmount']) : 0,
                        'details' => $productDetailsString
                    ];
                }
                $response['data'] = $responseOrders;
                $response['status'] = true;
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }
    
}
