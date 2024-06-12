<?php
namespace App;

class CategoryApi{

    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getCategoryList(){

        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from category";

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

    public function getCategorydetails($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * from category 
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

    public function addCategory($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "INSERT INTO category (Category, Description) 
                      values (:category,:description)";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':category', $params['category']);
            $stmt->bindParam(':description', $params['description']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New Category added successfully';
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

    public function updateCategory($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "UPDATE category SET Category=:category, Description= :description  WHERE id =:id ";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':category', $params['category']);
            $stmt->bindParam(':description', $params['description']);
            $stmt->bindParam(':id', $params['id']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Updated category successfully';
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

    public function deleteCategory($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "DELETE FROM category WHERE id= :id LIMIT 1";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Deleted successfully';
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

}

