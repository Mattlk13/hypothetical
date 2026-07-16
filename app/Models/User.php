<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Hash;
use App\Traits\Timestamp;

#[Fillable(['name', 'email', 'password', 'api_token'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    use Timestamp;

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

    /**
     * The default user profile image
     *
     * @var string
     */
    public static $default_profile_image = '/img/dashboard/missing-profile.png';

    /**
     * The directory user profile uploads are stored in
     *
     * @var string
     */
    public static $profile_image_dir = '/uploads/user/img/';

    /**
     * The maximum profile image width and height
     *
     * @var array
     */
    public static $profile_image_max = [
        'width' => 512,
        'height' => 512
    ];

    /**
     * Update the user's password
     *
     * @var string
     */
    public function updatePassword($oldpass, $newpass)
    {
        if (Hash::check($oldpass, $this->password)) {
            $this->password = Hash::make($newpass);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Get user profile image
     *
     * @var string
     */
    public function profileImage($show_full_path = false, $always_return_path = false)
    {
        $site_path = self::$profile_image_dir . $this->id . '-profile.png';
        $file_path = base_path() . '/public' . $site_path;

        if (file_exists($file_path) || $always_return_path) {
            return $show_full_path ? $file_path : $site_path;
        } else {
            return null;
        }
    }
}
