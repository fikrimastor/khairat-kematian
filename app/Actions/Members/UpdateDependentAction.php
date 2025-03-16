<?php

namespace App\Actions\Members;

use App\Models\Dependent;
use Illuminate\Support\Facades\DB;

class UpdateDependentAction
{
    /**
     * Update a dependent's information.
     *
     * @param int $dependentId The dependent ID
     * @param array $data The updated dependent data
     * @return Dependent The updated dependent
     */
    public function execute(int $dependentId, array $data): Dependent
    {
        return DB::transaction(function () use ($dependentId, $data) {
            $dependent = Dependent::findOrFail($dependentId);
            
            // Update the dependent record
            $dependent->update([
                'name' => $data['name'],
                'identification_number' => $data['identification_number'] ?? $dependent->identification_number,
                'birth_date' => $data['birth_date'] ?? $dependent->birth_date,
                'relationship' => $data['relationship'],
            ]);
            
            // Dispatch event if needed
            // DependentUpdated::dispatch($dependent);
            
            return $dependent;
        });
    }
} 