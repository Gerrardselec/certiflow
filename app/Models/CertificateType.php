<?php

namespace App\Models;

use App\Core\Model;

class CertificateType extends Model
{
    protected string $table = 'certificate_types';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'name', 'description', 'template_path', 'bs7671_compliant', 'active'
    ];
    
    /**
     * Get all active certificate types
     * 
     * @return array
     */
    public function getAllActive(): array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('active = ?', [1])
            ->orderBy('name ASC')
            ->fetch();
    }
    
    /**
     * Find a certificate type by name
     * 
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('name = ?', [$name])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Toggle the active status of a certificate type
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $type = $this->find($id);
        
        if (!$type) {
            return false;
        }
        
        $newActiveStatus = $type['active'] ? 0 : 1;
        
        return $this->queryBuilder
            ->update($this->table)
            ->set(['active' => $newActiveStatus])
            ->where('id = ?', [$id])
            ->execute();
    }
    
    /**
     * Validate certificate type data before saving
     * 
     * @param array $data
     * @return array Errors array, empty if validation passes
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        // Template path validation
        if (empty($data['template_path'])) {
            $errors['template_path'] = 'Template path is required';
        }
        
        return $errors;
    }
}