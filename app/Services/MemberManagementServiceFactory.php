<?php

namespace App\Services;

class MemberManagementServiceFactory
{
    /**
     * Create a new instance of the MemberManagementService
     *
     * @return MemberManagementService
     */
    public static function create(): MemberManagementService
    {
        return new MemberManagementService;
    }
}
