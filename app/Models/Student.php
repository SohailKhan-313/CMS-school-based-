<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_class_id',
        'admission_number',
        'roll_number',
        'name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'admission_date',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'address',
        'status',
        'photo',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo)) {
            return asset('storage/'.$this->photo);
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'admission_date' => 'date',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feeInvoices(): HasMany
    {
        return $this->hasMany(FeeInvoice::class);
    }

    public function totalFees(): float
    {
        return (float) $this->feeInvoices()->sum('total_amount');
    }

    public function paidFees(): float
    {
        return (float) $this->feeInvoices()->sum('paid_amount');
    }

    public function dueFees(): float
    {
        return max(0, $this->totalFees() - $this->paidFees());
    }
}
