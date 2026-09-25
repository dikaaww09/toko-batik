<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'email', 'password', 'whatsapp', 'alamat'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama'     => 'required|min_length[3]|max_length[120]',
        'email'    => 'required|valid_email|is_unique[customers.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'whatsapp' => 'required|regex_match[/^(?:\+62|62|0)8[0-9]{7,12}$/]',
        'alamat'   => 'required|min_length[10]|max_length[1000]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
