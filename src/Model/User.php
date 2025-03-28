<?php

namespace App\Model;

use App\Model\Database;
use Exception;
use PDO;

class User extends Database
{
    public function create(array $data)
    {
        $pdo = $this->getPdo();
        $pdo->beginTransaction();

        try {
            $person_type = $data['type'];
            $person = $data['person'];
            $address = $data['address'];
            $phone = $data['phone'];

            $sql = $person_type == "LEGAL"
                ? "INSERT INTO legal_people (cnpj, corporate_name, trade_name, state_registration) VALUES (:cnpj, :corporate_name, :trade_name, :state_registration)"
                : "INSERT INTO natural_people (cpf, dt_birth, gender) VALUES(:cpf, :dt_birth, :gender)";

            $stmt = $pdo->prepare($sql);
            if ($person_type == 'LEGAL') {
                $stmt->bindParam(":cnpj", $person['cnpj'], PDO::PARAM_STR);
                $stmt->bindParam(":corporate_name", $person['corporate_name'], PDO::PARAM_STR);
                $stmt->bindParam(":trade_name", $person['trade_name'], PDO::PARAM_STR);
                $stmt->bindParam(":state_registration", $person['state_registration'], PDO::PARAM_STR);
            } else {
                $stmt->bindParam(":cpf", $person['cpf'], PDO::PARAM_STR);
                $stmt->bindParam(":dt_birth", $person['dt_birth'], PDO::PARAM_STR);
                $stmt->bindParam(":gender", $person['gender'], PDO::PARAM_STR);
            }

            $stmt->execute();

            $person_id = $pdo->lastInsertId();

            if (!$person_id) {
                throw new Exception("Não foi possível criar a conta pois não foi possível cadastrar pessoa. Tente novamente mais tarde");
            }

            $sql = $person_type == 'LEGAL'
                ? "INSERT INTO users (username, email, password, id_legal_person) VALUES (:username, :email, :password, :id_person)"
                : "INSERT INTO users (username, email, password, id_natural_person) VALUES (:username, :email, :password, :id_person)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
            $stmt->bindParam(":id_person", $person_id, PDO::PARAM_INT);

            $stmt->execute();

            $user_id = $pdo->lastInsertId();

            if (!$user_id) {
                throw new Exception("Não foi possível criar a conta pois não foi possível cadastrar usuário. Tente novamente mais tarde");
            }

            $address = $this->registerAddress($address, $user_id, $pdo);

            if (!$address) {
                throw new Exception("Não foi possível criar uma conta, pois o endereço está com erro. Tente novamente mais tarde");
            }

            $phone = $this->registerPhone($phone, $user_id, $pdo);

            if (!$phone) {
                throw new Exception("Não foi possível criar a conta, pois telefone está com erro. Tente novamente mais tarde.");
            }

            $pdo->commit();

            return $user_id;
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function registerAddress(array $data, int $user_id, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? $this->getPdo();

        $sql = "INSERT INTO addresses (id_user, public_area, number, complement, district, city, state, zip_code) VALUES (:id_user, :public_area, :number, :complement,:district, :city, :state, :zip_code)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":complement", $data['complement'], PDO::PARAM_STR);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function registerPhone(array $data, int $user_id, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? $this->getPdo();

        $sql = "INSERT INTO phones (id_user, type, number) VALUES (:id_user, :type, :number)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":type", $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function select(array $data)
    {
        $pdo = $this->getPdo();

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

    public function updateAccess($data, $id)
    {
        $pdo = $this->getPdo();
        $sql = "UPDATE users SET password = :password WHERE id_user = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function activeUser($status, $id)
    {
        $pdo = $this->getPdo();

        $sql = "UPDATE users SET status = :status WHERE id_user = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function selectAll($page)
    {

        $limit = 25;
        $page = isset($page) ? (int) $page : 1;
        $offset = ($page - 1) * $limit;

        $pdo = $this->getPdo();

        $sql = "SELECT 
                u.username,
                u.email, 
                u.created_at,
                u.status,
                GROUP_CONCAT(DISTINCT CONCAT(p.type, ': ', p.number) SEPARATOR ' | ') AS phones,
                GROUP_CONCAT(DISTINCT CONCAT(a.public_area, ', ', a.number, ' - ', a.city, ' - ', a.state) SEPARATOR ' | ') AS addresses,
                CASE 
                    WHEN np.id_natural_person IS NOT NULL THEN 'Física' 
                    WHEN lp.id_legal_person IS NOT NULL THEN 'Jurídica' 
                    ELSE NULL 
                END AS person_type,
                np.dt_birth,
                np.gender,
                lp.corporate_name,
                lp.trade_name
            FROM ecommerce.users u
            LEFT JOIN ecommerce.natural_people np ON u.id_natural_person = np.id_natural_person
            LEFT JOIN ecommerce.legal_people lp ON u.id_legal_person = lp.id_legal_person
            LEFT JOIN ecommerce.phones p ON u.id_user = p.id_user
            LEFT JOIN ecommerce.addresses a ON u.id_user = a.id_user
            GROUP BY u.id_user
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset;
            ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTotalUsers()
    {
        $pdo = $this->getPdo();

        $sql = "SELECT COUNT(id_user) AS total FROM users";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getById(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT 
                u.id_user,
                u.username,
                u.email,
                CASE
                    WHEN np.id_natural_person IS NOT NULL THEN 'Física' 
                    WHEN lp.id_legal_person IS NOT NULL THEN 'Jurídica' 
                    ELSE NULL 
                END AS person_type,
                CASE
                    WHEN np.id_natural_person IS NOT NULL THEN np.cpf
                    ELSE NULL
                END AS cpf,
                CASE
                    WHEN np.id_natural_person IS NOT NULL THEN np.dt_birth
                    ELSE NULL
                END AS dt_birth,
                CASE
                    WHEN np.id_natural_person IS NOT NULL THEN np.gender
                    ELSE NULL
                END AS gender,
                CASE
                    WHEN lp.id_legal_person IS NOT NULL THEN lp.cnpj
                    ELSE NULL
                END AS cnpj,
                CASE
                    WHEN lp.id_legal_person IS NOT NULL THEN lp.corporate_name
                    ELSE NULL
                END AS corporate_name,
                CASE
                    WHEN lp.id_legal_person IS NOT NULL THEN lp.trade_name
                    ELSE NULL
                END AS trade_name,
                CASE
                    WHEN lp.id_legal_person IS NOT NULL THEN lp.state_registration
                    ELSE NULL
                END AS state_registration
            FROM users u
            LEFT JOIN ecommerce.natural_people np ON u.id_natural_person = np.id_natural_person
            LEFT JOIN ecommerce.legal_people lp ON u.id_legal_person = lp.id_legal_person
            WHERE u.id_user = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return  $stmt->fetch();
    }
}
