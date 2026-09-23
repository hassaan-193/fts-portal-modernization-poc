<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmcReportSystemItem extends Model
{
    protected $table = 'amc_report_system_items';

    protected $fillable = [
        'system_key',
        'item_slug',
        'item_label',
    ];
}
