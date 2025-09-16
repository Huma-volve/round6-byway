<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'description',
        'category_id',
        'instructor_id',
        'price',
        'compare_price',
        'image',
        'cover_public_id',
        'lessons_count',
        'level',
        'duration_hours',
        'total_minutes',
        'status',
    ];

    // Course belongs to an instructor (User)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Course belongs to ONE category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Course has MANY lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Course has MANY enrollments (students)
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Course has MANY reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Course can appear in MANY wishlists
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Course can be purchased as an order_item
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }


    public function orders()
{
    return $this->hasMany(Order::class, 'course_id');
}

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Enrollment::class,
            'course_id',
                'order_id',

        );
    }
}
