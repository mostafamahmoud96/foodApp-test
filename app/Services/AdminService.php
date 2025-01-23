<?php

namespace App\Services;

use App\Repositories\AdminRepository;


class AdminService
{

    public function __construct(public AdminRepository $adminRepository) {}

    /**
     * Get all admins emails
     * @return array
     */
    public function getAdminsEmails()
    {
        return $this->adminRepository->getAdminsEmails();
    }
}
