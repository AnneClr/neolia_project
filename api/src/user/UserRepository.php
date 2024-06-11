<?php
/**
 * UserRepository
 *  Simple repository to manage User entity
 * @version 1.0.0
 *  - findByLogin implementation
 */
namespace Api\User;

use Aelion\Dbal\DBAL;
use Aelion\Dbal\Exception\NotFoundException;
use Aelion\Dbal\Exception\IncorrectSqlExpressionException;
use Api\Account\AccountEntity;

class UserRepository {
    private \PDO $dbInstance;

    public function __construct() {
        $this->dbInstance = DBAL::getConnection();
    }

    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function findByLoginAndPassword(string $username, string $password): ?UserEntity {
        
        // Clean inputs
        $username = trim(htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));

        // Clean multiple spaces
        $username = preg_replace('/\s+/', ' ', $username);
        $username = str_replace(' ', '', $username);

        $sqlQuery = "SELECT 
            u.id userid, u.login login, u.password password, r.id roleid, r.role role, 
            a.id accountid, a.lastname lastname, a.firstname firstname, a.gender gender 
            FROM 
            user u 
            JOIN user_has_role uhr ON u.id = uhr.user_id 
            JOIN role r ON uhr.role_id = r.id
            JOIN account a ON u.id = a.user_id 
            WHERE login = :username;";
        
        $pdoStatement = $this->dbInstance->prepare($sqlQuery);
        $pdoStatement->bindParam(':username', $username, \PDO::PARAM_STR);
        $pdoStatement->execute();

        if ($pdoStatement) {
            $result = $pdoStatement->fetch(\PDO::FETCH_OBJ);
            if ($result) {
                if (password_verify($password, $result->password)) {
                    $user = new UserEntity();
                    $user->setId($result->userid);
                    $user->setLogin($result->login);
                    $user->setPassword($result->password);

                    $role = new RoleEntity();
                    $role->setId($result->roleid);
                    $role->setRole($result->role);
                    $user->addRole($role);

                    $account = new AccountEntity();
                    $account->setId($result->accountid);
                    $account->setLastname($result->lastname);
                    $account->setFirstname($result->firstname);
                    $account->setGender($result->gender);
                    $user->setAccount($account);

                    while ($result = $pdoStatement->fetch(\PDO::FETCH_OBJ)) {
                        $role = new RoleEntity();
                        $role->setId($result->roleid);
                        $role->setRole($result->role);
                        $user->addRole($role);                    
                    }
                    return $user;
                } else {
                    throw new NotFoundException('Incorrect password.');
                }
            } else {
                throw new NotFoundException('No user found with this login.');
            }
        } else {
            throw new IncorrectSqlExpressionException('Something went wrong while processing query: ' . $sqlQuery);
        }
    }
}
