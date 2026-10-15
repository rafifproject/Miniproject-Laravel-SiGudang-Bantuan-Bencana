<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'supplier_id',
        'distribution_target_id',
        'type',
        'quantity',
        'date',
        'reference',
        'notes',
        'is_public',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'date' => 'date',
        'is_public' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function distributionTarget(): BelongsTo
    {
        return $this->belongsTo(DistributionTarget::class);
    }
}
