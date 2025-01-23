<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository
{
    /**
     * @var Admin
     */
    public function __construct(public Admin $model) {}

    /**
     * Get all admins emails
     * @return array
     */
    public function getAdminsEmails()
    {
        return $this->model->pluck('email')->toArray();
    }
}
