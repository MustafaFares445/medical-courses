<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CourseAccessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CourseAccess extends Model
{
    /** @use HasFactory<CourseAccessFactory> */
    use HasFactory;

    protected $table = 'course_accesses';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'course_id',
        'order_item_id',
        'purchased_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchased_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return BelongsTo<OrderItem, $this> */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
