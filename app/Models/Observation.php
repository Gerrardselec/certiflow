<?php

namespace App\Models;

use App\Core\Model;

class Observation extends Model
{
    protected string $table = 'observations';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'certificate_id', 'code', 'description', 'recommendation',
        'priority', 'location'
    ];
    
    /**
     * Find all observations for a certificate
     * 
     * @param int $certificateId
     * @return array
     */
    public function findByCertificate(int $certificateId): array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('certificate_id = ?', [$certificateId])
            ->orderBy('priority ASC, id ASC')
            ->fetch();
    }
    
    /**
     * Find observations by priority
     * 
     * @param string $priority
     * @return array
     */
    public function findByPriority(string $priority): array
    {
        return $this->queryBuilder
            ->select('o.*, c.reference_number')
            ->from($this->table, 'o')
            ->join('certificates c', 'o.certificate_id = c.id')
            ->where('o.priority = ?', [$priority])
            ->orderBy('o.created_at DESC')
            ->fetch();
    }
    
    /**
     * Find C1 (dangerous condition) observations
     * 
     * @return array
     */
    public function findDangerousConditions(): array
    {
        return $this->findByPriority('C1');
    }
    
    /**
     * Create multiple observations at once
     * 
     * @param array $observations Array of observation data arrays
     * @return bool
     */
    public function createMultiple(array $observations): bool
    {
        $success = true;
        
        foreach ($observations as $observation) {
            if (!$this->create($observation)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Validate observation data before saving
     * 
     * @param array $data
     * @return array Errors array, empty if validation passes
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Certificate ID validation
        if (empty($data['certificate_id'])) {
            $errors['certificate_id'] = 'Certificate ID is required';
        }
        
        // Code validation
        if (empty($data['code'])) {
            $errors['code'] = 'Code is required';
        }
        
        // Description validation
        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        }
        
        // Recommendation validation
        if (empty($data['recommendation'])) {
            $errors['recommendation'] = 'Recommendation is required';
        }
        
        // Priority validation
        if (empty($data['priority'])) {
            $errors['priority'] = 'Priority is required';
        } elseif (!in_array($data['priority'], ['C1', 'C2', 'C3', 'FI'])) {
            $errors['priority'] = 'Priority must be C1, C2, C3, or FI';
        }
        
        return $errors;
    }
}