<?php

namespace App\Service\Base;

use App\Helpers\DatabaseErrorHelpers;
use Exception;
use PDO;
use PDOException;

abstract class BaseService{
    protected PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    protected function execute(callable $callback, ?bool $useTransaction = null)
    {
        try {
            if ($useTransaction === true) {
                $this->pdo->beginTransaction();
            }
    
            $result = $callback();
    
            if ($useTransaction === true) {
                $this->pdo->commit();
            }
    
            return $result;
        } catch (PDOException $e) {
            if ($useTransaction === true && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            if ($useTransaction === true && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['error' => $e->getMessage()];
        }
    }
}