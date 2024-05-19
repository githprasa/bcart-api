<?php
namespace App;

class ProductApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function productResult() {
        $response=[];
        try {
            $conn = $this->db->connect();
        } catch (\Exception $e) {
            $response['message']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;
            return $response;
        } finally {
            $this->db->close();
        }
    }
}
