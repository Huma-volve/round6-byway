<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }





    // A user may have ONE instructor profile
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

    // A user may have ONE student profile
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    // An instructor can create MANY courses
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // A student can enroll in MANY courses
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    // A user can leave MANY reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // A user can place MANY orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // A user can have MANY payments THROUGH orders
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    // A user can have MANY wishlisted courses
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
