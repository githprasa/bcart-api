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
            $query = "SELECT Id,FirstName,CostObject,RoleId,Email,LocationId,Currency FROM users WHERE UserId = :user AND Password = SHA1(:password)";
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

    public function addUser($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "INSERT INTO users (FirstName, LastName,RoleId,Email,Phone,Address1,Address2,UserId,Password,LocationId,CostObject,Currency) 
                      values (:FirstName, :LastName,:RoleId,:Email,:Phone,:Address1,:Address2,:UserId,SHA1(:Password),:LocationId,:CostObject,:Currency)";

            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':FirstName', $params['FirstName']);
            $stmt->bindParam(':LastName', $params['LastName']);
            $stmt->bindParam(':RoleId', $params['RoleId']);
            $stmt->bindParam(':Email', $params['Email']);
            $stmt->bindParam(':Phone', $params['Phone']);
            $stmt->bindParam(':Address1', $params['Address1']);
            $stmt->bindParam(':Address2', $params['Address2']);
            $stmt->bindParam(':UserId', $params['UserId']);
            $stmt->bindParam(':Password', $params['Password']);
            $stmt->bindParam(':LocationId', $params['LocationId']);
            $stmt->bindParam(':CostObject', $params['CostObject']);
            $stmt->bindParam(':Currency', $params['Currency']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='New user added successfully';
            }else{
                $response['data']='Failed to add user';
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

    public function updateUser($params){
        $response=[];
        $response['status']=false;
        $response['data']='';
        try {
            $query = "UPDATE users SET FirstName=:FirstName, LastName= :LastName, RoleId = :RoleId,
            Email = :Email,Phone=:Phone,Address1=:Address1,Address2=:Address2,UserId=:UserId,
            Password=SHA1(:Password),LocationId=:LocationId,CostObject=:CostObject ,Currency=:Currency WHERE Id =:id ";
            if(trim($params['Password'])=='') {
                $query = "UPDATE users SET FirstName=:FirstName, LastName= :LastName, RoleId = :RoleId,
                Email = :Email,Phone=:Phone,Address1=:Address1,Address2=:Address2,UserId=:UserId,
                LocationId=:LocationId,CostObject=:CostObject ,Currency=:Currency WHERE Id =:id ";
            }


            $conn = $this->db->connect();            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':FirstName', $params['FirstName']);
            $stmt->bindParam(':LastName', $params['LastName']);
            $stmt->bindParam(':RoleId', $params['RoleId']);
            $stmt->bindParam(':Email', $params['Email']);
            $stmt->bindParam(':Phone', $params['Phone']);
            $stmt->bindParam(':Address1', $params['Address1']);
            $stmt->bindParam(':Address2', $params['Address2']);
            $stmt->bindParam(':UserId', $params['UserId']);
            if(trim($params['Password']) !='') {
            $stmt->bindParam(':Password', $params['Password']);
            }
            $stmt->bindParam(':LocationId', $params['LocationId']);
            $stmt->bindParam(':CostObject', $params['CostObject']);
            $stmt->bindParam(':Currency', $params['Currency']);
            $stmt->bindParam(':id', $params['id']);
            $result = $stmt->execute();
            if($result){
                $response['status']=true;
                $response['data']='Updated user successfully';
            }else{
                $response['data']='Updated user failed';
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

    public function deleteUser($userid) {
        $response = [];
        $response['status'] = false;
        try {
            $query = "DELETE FROM users WHERE Id = :id";
            $conn = $this->db->connect();
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $userid);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $response['status'] = true;
                $response['message'] = 'User deleted successfully';
            } else {
                $response['message'] = 'No user found';
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $this->db->close();
            return $response;
        }
    }

}
