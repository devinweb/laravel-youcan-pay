<?php

namespace Devinweb\LaravelYoucanPay\Models;

use Devinweb\LaravelYoucanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYoucanPay\LaravelYoucanPay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Devinweb\LaravelYoucanPay\Database\Factories\TransactionFactory;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<array-key, string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The status should be cast to the native types.
     *
     * @var array
     */
    protected $enums = [
        'status' => YouCanPayStatus::class,
    ];

    /**
     * Get the user that owns the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->owner();
    }

    /**
     * Get the model related to the transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        $model = LaravelYoucanPay::$customerModel;

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    /**
     * Determine if the transaction is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == YouCanPayStatus::pending();
    }

    /**
     * Determine if the transaction is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status == YouCanPayStatus::paid();
    }
    
    /**
     * Determine if the transaction is paid.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == YouCanPayStatus::failed();
    }

    /**
     * Filter query by paid.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePaid($query)
    {
        $query->where('status', YouCanPayStatus::PAID());
    }
    
    /**
     * Filter query by failed.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeFailed($query)
    {
        $query->where('status', YouCanPayStatus::FAILED());
    }
    
    /**
     * Filter query by pending.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePending($query)
    {
        $query->where('status', YouCanPayStatus::PENDING());
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return TransactionFactory::new();

        return TransactionFactory::new();
    }
}
