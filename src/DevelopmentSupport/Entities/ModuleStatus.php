<?php

declare(strict_types=1);

namespace mpba\Modules\DevelopmentSupport\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModuleStatus
 */
class ModuleStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module',
        'enabled',
        'description',
        'version',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];
}
