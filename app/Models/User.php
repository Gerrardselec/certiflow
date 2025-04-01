<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'username', 'password', 'email', 'first_name', 'last_name',
        'role_id', 'active'
    ];
    
    /**
     * Find a user by their username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('username = ?', [$username])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Find a user by their email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('email = ?', [$email])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Get the user's role
     * 
     * @param int $userId
     * @return array|null
     */
    public function getRole(int $userId): ?array
    {
        return $this->queryBuilder
            ->select('r.*')
            ->from($this->table, 'u')
            ->join('roles r', 'u.role_id = r.id')
            ->where('u.id = ?', [$userId])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Get the user's permissions
     * 
     * @param int $userId
     * @return array
     */
    public function getPermissions(int $userId): array
    {
        return $this->queryBuilder
            ->select('p.*')
            ->from($this->table, 'u')
            ->join('role_permissions rp', 'u.role_id = rp.role_id')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('u.id = ?', [$userId])
            ->fetch();
    }
    
    /**
     * Check if user has a specific permission
     * 
     * @param int $userId
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(int $userId, string $permissionName): bool
    {
        $result = $this->queryBuilder
            ->select('COUNT(*) as count')
            ->from($this->table, 'u')
            ->join('role_permissions rp', 'u.role_id = rp.role_id')
            ->join('permissions p', 'rp.permission_id = p.id')
            ->where('u.id = ? AND p.name = ?', [$userId, $permissionName])
            ->limit(1)
            ->fetchOne();
            
        return $result['count'] > 0;
    }
    
    /**
     * Update the last login time for a user
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->queryBuilder
            ->update($this->table)
            ->set(['last_login' => date('Y-m-d H:i:s')])
            ->where('id = ?', [$userId])
            ->execute();
    }
    
    /**
     * Validate user data before saving
     * 
     * @param array $data
     * @return array Errors array, empty if validation passes
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid';
        }
        
        // Password validation (for new users or password changes)
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
        }
        
        // First name validation
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        // Last name validation
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        return $errors;
    }
}