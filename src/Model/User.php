<?php

namespace App\Model;

use App\Model\Database;
use Exception;
use PDO;

class User extends Database
{
    public static function create(array $data)
    {
        $pdo = self::getConnection();
        $pdo->beginTransaction();

        try{
            $person_type = $data['type'];
            $person = $data['person'];
            $address = $data['address'];
            $phone = $data['phone'];

            $sql = $person_type == "LEGAL" 
                ? "INSERT INTO LEGAL_PEOPLE (cnpj, corporate_name, trade_name, state_registration) VALUES (:cnpj, :corporate_name, :trade_name, :state_registration)"
                : "INSERT INTO NATURAL_PEOPLE (cpf, dt_birth, gender) VALUES(:cpf, :dt_birth, :gender)";
               
            $stmt = $pdo->prepare($sql);    
            if($person_type == 'LEGAL'){
                $stmt->bindParam(":cnpj", $person['cnpj'], PDO::PARAM_STR);
                $stmt->bindParam(":corporate_name", $person['corporate_name'], PDO::PARAM_STR);
                $stmt->bindParam(":trade_name", $person['trade_name'], PDO::PARAM_STR);
                $stmt->bindParam(":state_registration", $person['state_registration'], PDO::PARAM_STR);
            }else{
                $stmt->bindParam(":cpf", $person['cpf'], PDO::PARAM_STR);
                $stmt->bindParam(":dt_birth", $person['dt_birth'], PDO::PARAM_STR);
                $stmt->bindParam(":gender", $person['gender'], PDO::PARAM_STR);
            }

            $stmt->execute();

            $person_id = $pdo->lastInsertId();

            if(!$person_id){
                throw new Exception("Não foi possível criar a conta pois não foi possível cadastrar pessoa. Tente novamente mais tarde");
            }

            $sql = $person_type == 'LEGAL'
            ? "INSERT INTO USERS (username, email, password, id_legal_person) VALUES (:username, :email, :password, :id_person)"
            : "INSERT INTO USERS (username, email, password, id_natural_person) VALUES (:username, :email, :password, :id_person)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
            $stmt->bindParam(":id_person", $person_id, PDO::PARAM_INT);

            $stmt->execute();

            $user_id = $pdo->lastInsertId();

            if(!$user_id){
                throw new Exception("Não foi possível criar a conta pois não foi possível cadastrar usuário. Tente novamente mais tarde");
            }

            $address = self::registerAddress($address, $user_id, $pdo);

            if(!$address){
                throw new Exception("Não foi possível criar uma conta, pois o endereço está com erro. Tente novamente mais tarde");
            }

            $phone = self::registerPhone($phone, $user_id, $pdo);

            if(!$phone){
                throw new Exception("Não foi possível criar a conta, pois telefone está com erro. Tente novamente mais tarde.");
            }

            $pdo->commit();

            return $user_id;
        }catch(Exception $e){
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public static function registerAddress(array $data, int $user_id, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? self::getConnection();

        $sql = "INSERT INTO ADDRESSES (id_user, public_area, number, district, city, state, zip_code) VALUES (:id_user, :public_area, :number, :district, :city, :state, :zip_code)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    
    }

    public static function registerPhone(array $data, int $user_id, ?PDO $pdo = null)
    {   
        $pdo = $pdo ?? self::getConnection();

        $sql = "INSERT INTO PHONES (id_user, type, number) VALUES (:id_user, :type, :number)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function login(array $data)
    {
        $pdo = self::getConnection();

        $sql = "SELECT 
                u.*, 
                CASE 
                    WHEN np.id_natural_person IS NOT NULL THEN 'Física' 
                    WHEN lp.id_legal_person IS NOT NULL THEN 'Jurídica' 
                    ELSE NULL 
                END AS person_type
            FROM ecommerce.users u
            LEFT JOIN ecommerce.natural_people np ON u.id_natural_person = np.id_natural_person
            LEFT JOIN ecommerce.legal_people lp ON u.id_legal_person = lp.id_legal_person
            WHERE (np.cpf = :login OR lp.cnpj = :login OR u.email = :login)
            LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":login", $data['login'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function updateAccess($data, $id)
    {
        $pdo = self::getConnection();
        $sql = "UPDATE USERS SET password = :password WHERE id_user = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function activeUser($status, $id)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE USERS SET status = :status WHERE id_user = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
