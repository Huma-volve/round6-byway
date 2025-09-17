<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory,Searchable;


    protected $fillable = [
        'title',
        'description',
        'category_id',
        'instructor_id',
        'price',
        'compare_price',
        'image',
        'lessons_count',
        'level',
        'duration_hours',
        'total_minutes',
        'status',
    ];

    public function toSearchableArray()
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'level'       => $this->level,
            'status'      => $this->status,
            'category'    => $this->category->name ?? null,
            'instructor'  => $this->instructor->first_name . ' ' . $this->instructor->last_name ?? null,
            'rating'      => $this->reviews()->avg('rating') ?? 0,
        ];
    }

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
