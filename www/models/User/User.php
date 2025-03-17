<?php

namespace Models\User;

use Exception;

class User extends \Models\Model
{
    public function __construct()
    {
        $this->getConnection('main');
    }

    /**
     *  Return username informations
     */
    public function get(int $id)
    {
        $data = '';

        try {
            $stmt = $this->db->prepare("SELECT users.Id as userId, users.Username, users.First_name, users.Last_name, users.Email, user_role.Name as Role_name
            FROM users JOIN user_role ON users.Role = user_role.Id
            WHERE users.Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row;
        }

        return $data;
    }

    /**
     *  Return users list from database
     */
    public function getUsers() : array
    {
        $data = array();

        try {
            $result = $this->db->query("SELECT users.Id, users.Username, users.First_name, users.Last_name, users.Email, users.Type, user_role.Name as Role_name
            FROM users JOIN user_role ON users.Role = user_role.Id
            ORDER BY Username ASC");
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     *  Get username by user Id
     */
    public function getUsernameById(string $id) : string
    {
        $data = '';

        try {
            $stmt = $this->db->prepare("SELECT Username FROM users WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e);
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row['Username'];
        }

        return $data;
    }

    /**
     *  Get role by user Id
     */
    public function getRoleById(string $id) : string
    {
        $data = '';

        try {
            $stmt = $this->db->prepare("SELECT Role FROM users WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e);
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row['Role'];
        }

        return $data;
    }

    /**
     *  Get user Id by username
     */
    public function getIdByUsername(string $username) : int|null
    {
        $data = null;

        try {
            $stmt = $this->db->prepare("SELECT Id FROM users WHERE Username = :username");
            $stmt->bindValue(':username', $username);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row['Id'];
        }

        return $data;
    }

    /**
     *  Add a new user in database
     */
    public function add(string $username, string $hashedPassword, string $role) : void
    {
        try {
            // Create new user
            $stmt = $this->db->prepare("INSERT INTO users ('Username', 'Password', 'First_name', 'Role', 'State', 'Type') VALUES (:username, :password, :first_name, :role, 'active', 'local')");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':first_name', $username);
            $stmt->bindValue(':role', $role);
            $stmt->execute();

            // Set empty set of permissions for new user
            $stmt = $this->db->prepare("INSERT INTO user_permissions (Permissions, User_id) VALUES ('{}', :id)");
            $stmt->bindValue(':id', $this->db->lastInsertRowID());
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Delete user from database
     */
    public function delete(string $id) : void
    {
        try {
            // Set user status on 'deleted' in database (keep the user for history purpose)
            // $stmt = $this->db->prepare("UPDATE users SET State = 'deleted', Password = null WHERE Id = :id");
            // $stmt->bindValue(':id', $id);
            // $result = $stmt->execute();

            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();

            // Delete user permissions
            $stmt = $this->db->prepare("DELETE FROM user_permissions WHERE User_id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Update user personnal info in database
     */
    public function edit(int $id, string $firstName = null, string $lastName = null, string $email = null) : void
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET First_name = :firstName, Last_name = :lastName, Email = :email WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':firstName', $firstName);
            $stmt->bindValue(':lastName', $lastName);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Return true if user Id exists in database
     */
    public function existsId(int $id) : bool
    {
        try {
            $stmt = $this->db->prepare("SELECT Id FROM users WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        if ($this->db->isempty($result)) {
            return false;
        }

        return true;
    }

    /**
     *  Return true if user username exists in database
     */
    public function exists(string $username) : bool
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE Username = :username");
            $stmt->bindValue(':username', $username);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        if ($this->db->isempty($result)) {
            return false;
        }

        return true;
    }

    /**
     *  Update user password in database
     */
    public function updatePassword(int $id, string $hashedPassword) : void
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET Password = :password WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }
    }

    /**
     *  Return specified username hashed password from db
     */
    public function getHashedPasswordFromDb(int $id) : string
    {
        $data = '';

        try {
            $stmt = $this->db->prepare("SELECT Password FROM users WHERE Id = :id");
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
        } catch (Exception $e) {
            $this->db->logError($e->getMessage());
        }

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data = $row['Password'];
        }

        return $data;
    }
}
