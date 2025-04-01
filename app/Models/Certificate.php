<?php

namespace App\Models;

use App\Core\Model;

class Certificate extends Model
{
    protected string $table = 'certificates';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'reference_number', 'certificate_type_id', 'customer_id',
        'installation_address_line1', 'installation_address_line2',
        'installation_city', 'installation_postal_code', 'installation_county',
        'created_by', 'engineer_id', 'issue_date', 'expiry_date',
        'status', 'certificate_data', 'pdf_path'
    ];
    
    /**
     * Find a certificate by reference number
     * 
     * @param string $referenceNumber
     * @return array|null
     */
    public function findByReferenceNumber(string $referenceNumber): ?array
    {
        return $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('reference_number = ?', [$referenceNumber])
            ->limit(1)
            ->fetchOne();
    }
    
    /**
     * Get all certificates for an engineer
     * 
     * @param int $engineerId
     * @return array
     */
    public function findByEngineer(int $engineerId): array
    {
        return $this->queryBuilder
            ->select('c.*, ct.name as certificate_type, cu.company_name, cu.first_name, cu.last_name')
            ->from($this->table, 'c')
            ->join('certificate_types ct', 'c.certificate_type_id = ct.id')
            ->join('customers cu', 'c.customer_id = cu.id')
            ->where('c.engineer_id = ?', [$engineerId])
            ->orderBy('c.issue_date DESC')
            ->fetch();
    }
    
    /**
     * Generate a new unique reference number
     * 
     * @return string
     */
    public function generateReferenceNumber(): string
    {
        // Get certificate prefix from settings
        $settingsModel = new \App\Models\Setting();
        $prefix = $settingsModel->getValueByKey('certificate_prefix', 'GE-');
        
        // Generate a timestamp-based unique identifier
        $timestamp = date('YmdHis');
        $random = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4);
        
        $referenceNumber = $prefix . $timestamp . $random;
        
        // Ensure it doesn't already exist
        while ($this->findByReferenceNumber($referenceNumber)) {
            $random = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4);
            $referenceNumber = $prefix . $timestamp . $random;
        }
        
        return $referenceNumber;
    }
    
    /**
     * Get all observations for a certificate
     * 
     * @param int $certificateId
     * @return array
     */
    public function getObservations(int $certificateId): array
    {
        $observationModel = new \App\Models\Observation();
        return $observationModel->findByCertificate($certificateId);
    }
    
    /**
     * Update certificate status
     * 
     * @param int $certificateId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $certificateId, string $status): bool
    {
        $allowedStatuses = ['draft', 'issued', 'expired', 'superseded'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        
        return $this->queryBuilder
            ->update($this->table)
            ->set(['status' => $status])
            ->where('id = ?', [$certificateId])
            ->execute();
    }
    
    /**
     * Get certificates expiring soon
     * 
     * @param int $daysAhead
     * @return array
     */
    public function getExpiringCertificates(int $daysAhead = 30): array
    {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime("+{$daysAhead} days"));
        
        return $this->queryBuilder
            ->select('c.*, ct.name as certificate_type, cu.company_name, cu.first_name, cu.last_name, cu.email')
            ->from($this->table, 'c')
            ->join('certificate_types ct', 'c.certificate_type_id = ct.id')
            ->join('customers cu', 'c.customer_id = cu.id')
            ->where('c.expiry_date BETWEEN ? AND ? AND c.status = ?', [$today, $futureDate, 'issued'])
            ->orderBy('c.expiry_date ASC')
            ->fetch();
    }
    
    /**
     * Validate certificate data before saving
     * 
     * @param array $data
     * @return array Errors array, empty if validation passes
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Certificate type validation
        if (empty($data['certificate_type_id'])) {
            $errors['certificate_type_id'] = 'Certificate type is required';
        }
        
        // Customer validation
        if (empty($data['customer_id'])) {
            $errors['customer_id'] = 'Customer is required';
        }
        
        // Installation address validation
        if (empty($data['installation_address_line1'])) {
            $errors['installation_address_line1'] = 'Installation address is required';
        }
        
        if (empty($data['installation_city'])) {
            $errors['installation_city'] = 'Installation city is required';
        }
        
        if (empty($data['installation_postal_code'])) {
            $errors['installation_postal_code'] = 'Installation postal code is required';
        }
        
        // Engineer validation
        if (empty($data['engineer_id'])) {
            $errors['engineer_id'] = 'Engineer is required';
        }
        
        // Issue date validation
        if (empty($data['issue_date'])) {
            $errors['issue_date'] = 'Issue date is required';
        } elseif (!$this->validateDate($data['issue_date'])) {
            $errors['issue_date'] = 'Issue date is invalid';
        }
        
        // Expiry date validation if provided
        if (!empty($data['expiry_date']) && !$this->validateDate($data['expiry_date'])) {
            $errors['expiry_date'] = 'Expiry date is invalid';
        }
        
        // Certificate data validation
        if (empty($data['certificate_data'])) {
            $errors['certificate_data'] = 'Certificate data is required';
        } elseif (is_array($data['certificate_data'])) {
            // Convert array to JSON string
            $data['certificate_data'] = json_encode($data['certificate_data']);
        } elseif (!$this->isValidJson($data['certificate_data'])) {
            $errors['certificate_data'] = 'Certificate data must be valid JSON';
        }
        
        return $errors;
    }
    
    /**
     * Validate a date string
     * 
     * @param string $date
     * @param string $format
     * @return bool
     */
    private function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Check if a string is valid JSON
     * 
     * @param string $string
     * @return bool
     */
    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}