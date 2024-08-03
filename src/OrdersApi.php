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
            $date = date("Y-m-d H:i:s");
            $stmt->bindParam(':orderDate', $date);
            $stmt->bindParam(':totalAmount', $param['totalAmount']);
            $stmt->bindParam(':apiCallStatus', $param['apiCallStatus']);
            $stmt->bindParam(':UserId', $param['UserId']);
            $stmt->execute();
            $lastInsertId = $conn->lastInsertId();
            // $response['lastInsertId']= $lastInsertId;
            if ($lastInsertId) {
                $query = "SELECT * FROM cartitems WHERE UserId = :UserId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':UserId', $param['UserId']);
                $stmt->execute();
                $cartItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $query = "SELECT * FROM settings";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $fetchsettings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (count($cartItems) > 0) {
                    $allProductData = [];
                    foreach ($cartItems as $cartItem) {
                        $ProductId = $cartItem['ProductId'] ?? 0;
                        $VendorId = $cartItem['VendorId'] ?? 0;
                        $LocationId = $cartItem['Location'] ?? 0;
        
                        $query = "SELECT * FROM product WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $ProductId);
                        $stmt->execute();
                        $productItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $allProductData = array_merge($allProductData, $productItems);

                        $Price = $productItems[0]['price']? ($productItems[0]['price'] * $cartItem['Quantity']) : 0;
                        // $response['Price'] = $Price;
                        $jsonProductItems = count($productItems) > 0 ? json_encode($productItems) : json_encode([$ProductId]);
                        
                        $query = "SELECT * FROM vendor WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $VendorId);
                        $stmt->execute();
                        $vendorItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $jsonvendorItems = count($vendorItems) > 0 ? json_encode($vendorItems) : json_encode([$VendorId]);
                        // $response['jsonvendorItems'] = $jsonvendorItems;

                        $query = "SELECT * FROM locations WHERE Id = :Id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':Id', $LocationId);
                        $stmt->execute();
                        $locationItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $jsonlocationItems = count($locationItems) > 0 ? json_encode($locationItems) : json_encode([$LocationId]);
                        // $response['jsonlocationItems'] = $jsonlocationItems;

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
                    if (is_array($fetchsettings) && count($fetchsettings) >0) {
                        foreach ($fetchsettings as $setting) {
                            $AdditionalFields = $setting["AdditionalFields"] ?? '';
                            $fields = explode(',', $AdditionalFields);
                            $fileType = $setting["FileType"] ?? 'json';

                            $extractedData = [];
                            if(is_array($allProductData) && count($allProductData) >0) {
                                foreach ($allProductData as $record) {
                                    $extractedRecord = [];
                                    if(is_array($fields) && count($fields) >0) {
                                        foreach ($fields as $field) {
                                            $extractedRecord[$field] = $record[$field] ?? '';
                                        }
                                        $extractedData[] = $extractedRecord;
                                    }
                                }
                            }
                            $filenameAppend = $lastInsertId."_".date('Ymd_His');
                            if ($fileType === "json") {
                                $jsonFileName = "orderfiles/product_data_".$filenameAppend.".json";
                                file_put_contents($jsonFileName, json_encode($extractedData, JSON_PRETTY_PRINT));
                                $response['filestatus'] = 'JSON file created:'.$jsonFileName;
                            } elseif ($fileType === "text") {
                                $textFileName = "orderfiles/product_data_".$filenameAppend.".txt";
                                $fileHandle = fopen($textFileName, 'w');
                                foreach ($extractedData as $record) {
                                    $line = implode(", ", array_map(
                                        function ($key, $value) {
                                            return "$key: $value";
                                        },
                                        array_keys($record),
                                        $record
                                    ));
                                    fwrite($fileHandle, $line . PHP_EOL);
                                }
                                fclose($fileHandle);
                                $response['filestatus'] = 'Text file created:'.$textFileName;
                            }
                        }
                    }
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
