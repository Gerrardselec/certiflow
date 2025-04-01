<?php

namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    protected string $table = 'customers';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'company_name', 'first_name', 'last_name', 'email', 'phone',
        'address_line1', 'address_line2', 'city', 'postal_code', 'county',
        'country', 'created_by', 'active', 'consent_marketing',
        'consent_data_processing', 'consent_date', 'portal_access'
    ];
    
    /**
     * Find a customer by their email
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
     * Find customers by company name
     * 
     * @param string $companyName
     * @return array
     */
    public function findByCompanyName(string $companyName): array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('company_name LIKE ?', ["%{$companyName}%"])
            ->orderBy('company_name ASC')
            ->fetch();
    }
    
    /**
     * Get all certificates for a customer
     * 
     * @param int $customerId
     * @return array
     */
    public function getCertificates(int $customerId): array
    {
        return $this->queryBuilder
            ->select('c.*, ct.name as certificate_type')
            ->from('certificates', 'c')
            ->join('certificate_types ct', 'c.certificate_type_id = ct.id')
            ->where('c.customer_id = ?', [$customerId])
            ->orderBy('c.issue_date DESC')
            ->fetch();
    }
    
    /**
     * Get customer access details if they have portal access
     * 
     * @param int $customerId
     * @return array|null
     */
    public function getPortalAccess(int $customerId): ?array
    {
        return $this->queryBuilder
            ->select('*')
            ->from('customer_access')
            ->where('customer_id = ?', [$customerId])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Set portal access for a customer
     * 
     * @param int $customerId
     * @param bool $hasAccess
     * @return bool
     */
    public function setPortalAccess(int $customerId, bool $hasAccess): bool
    {
        return $this->queryBuilder
            ->update($this->table)
            ->set(['portal_access' => $hasAccess ? 1 : 0])
            ->where('id = ?', [$customerId])
            ->execute();
    }
    
    /**
     * Validate customer data before saving
     * 
     * @param array $data
     * @return array Errors array, empty if validation passes
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // First name validation
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        // Last name validation
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid';
        }
        
        // Address validation
        if (empty($data['address_line1'])) {
            $errors['address_line1'] = 'Address line 1 is required';
        }
        
        if (empty($data['city'])) {
            $errors['city'] = 'City is required';
        }
        
        if (empty($data['postal_code'])) {
            $errors['postal_code'] = 'Postal code is required';
        }
        
        return $errors;
    }
}