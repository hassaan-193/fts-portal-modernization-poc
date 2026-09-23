<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReport extends Model
{
    /** Saved from the visit form by its author but not yet submitted for approval. */
    const STATUS_DRAFT = 'draft';

    protected $table = 'projectreports';

    protected $fillable = [
        'reference_number', 'date', 'inspector_visiting_time', 'inspector_leaving_time',
        'company_id', 'is_manual_client', 'manual_client_name',
        'project_id', 'visit_schedule_id', 'is_emergency_visit', 'emergency_visit_date',
        'amc_type', 'site_location', 'site_name',
        'notes_used_items', 'next_inspection_due', 'expiry_update_required_on',
        'client_eid_details', 'client_phone', 'client_signature', 'status',
        'fire_alarm_data', 'fire_fighting_data', 'fm200_data', 'foam_tank_data',
        'voice_evacuation_data', 'emergency_lighting_data', 'system_interfacing_data',
        'exit_route_data', 'storage_conditions_data', 'pump_data', 'deluge_data',
        'urgent_summary', 'photos_data','file_path','used_items','required_items','block_info', 'created_by'
    ];

    protected $casts = [
        'fire_alarm_data' => 'array',
        'fire_fighting_data' => 'array',
        'fm200_data' => 'array',
        'foam_tank_data' => 'array',
        'voice_evacuation_data' => 'array',
        'emergency_lighting_data' => 'array',
        'system_interfacing_data' => 'array',
        'exit_route_data' => 'array',
        'storage_conditions_data' => 'array',
        'pump_data' => 'array',
        'deluge_data' => 'array',
        'urgent_summary' => 'array',
        'photos_data' => 'array',
        'file_path' => 'array',
        'block_info' => 'array',
        'is_manual_client' => 'boolean',
        'is_emergency_visit' => 'boolean',

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function visitSchedule()
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_schedule_id');
    }

    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
