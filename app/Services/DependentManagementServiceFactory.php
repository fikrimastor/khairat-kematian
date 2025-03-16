<?php

namespace App\Services;

class DependentManagementServiceFactory
{
    /**
     * Create a new instance of the DependentManagementService
     *
     * @return DependentManagementService
     */
    public static function create(): DependentManagementService
    {
        return new DependentManagementService;
    }
}
