<?php
namespace App;

class UserApi {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function UserLogin($user, $password) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * FROM users WHERE UserId = :user AND Password = SHA1(:password)";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $row_result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if(count($row_result) >0) {
                $response['status']=true;
                $response['data']=$row_result;
            }
        } catch (\Exception $e) {
            $response['data']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function getUser($id) {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * FROM users WHERE Id = :id";
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
            $response['data']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;
        } finally {
            $this->db->close();
            return $response;
        }
    }

    public function getUsers() {
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "SELECT * FROM users";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $row_result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if(count($row_result) >0) {
                $response['status']=true;
                $response['data']=$row_result;
            }
        } catch (\Exception $e) {
            $response['data']='Error : ' . $e->getMessage();
            $response['file']= $e->getFile();
            $response['line number']=$e->getLine();
            $response['logResult']=-1;
        } finally {
            $this->db->close();
            return $response;
        }
    }

}
