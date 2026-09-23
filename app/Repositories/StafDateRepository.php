<?php

namespace App\Repositories;

use App\Models\StafDates;
use Illuminate\Support\Facades\Log;

class StafDateRepository
{
    protected $model;

    public function __construct(StafDates $model)
    {
        $this->model = $model;
    }

    /**
     * Get all staff dates with pagination
     */
    public function all($limit = 15)
    {
        return $this->model->paginate($limit);
    }

    /**
     * Get all dates for a specific staff member
     */
    public function getByStaff($staffId)
    {
        return $this->model->where('staff_id', $staffId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Find a record by ID
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new staff date record
     */
    public function create($data)
    {
        try {
            // Calculate days between dates if end_date is provided
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $startDate = \Carbon\Carbon::parse($data['start_date']);
                $endDate = \Carbon\Carbon::parse($data['end_date']);
                $data['days'] = $startDate->diffInDays($endDate) + 1;
            }

            return $this->model->create($data);
        } catch (\Exception $e) {
            Log::error('StafDateRepository@create error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a staff date record
     */
    public function update($data, $id)
    {
        try {
            $record = $this->model->find($id);
            
            if (!$record) {
                return false;
            }

            // Calculate days between dates if end_date is provided
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $startDate = \Carbon\Carbon::parse($data['start_date']);
                $endDate = \Carbon\Carbon::parse($data['end_date']);
                $data['days'] = $startDate->diffInDays($endDate) + 1;
            }

            return $record->update($data);
        } catch (\Exception $e) {
            Log::error('StafDateRepository@update error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a staff date record
     */
    public function delete($id)
    {
        try {
            $record = $this->model->find($id);
            
            if (!$record) {
                return false;
            }

            return $record->delete();
        } catch (\Exception $e) {
            Log::error('StafDateRepository@delete error: ' . $e->getMessage());
            throw $e;
        }
    }
}
