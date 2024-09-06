<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filament\Resources\ShopResource;
use App\Mail\BrowseAdmin;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;



class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use HasPanelShield;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'gender',
        'phone',
        'avatar',
        'province_id',
        'district_id',
        'ward_id',
        'shop_id',
        'verification_code',
        'payment_method',
        'created_by',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function province(): BelongsTo
    {
        return $this->BelongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->BelongsTo(District::class);
    }

    public function ward(): BelongsTo
    {
        return $this->BelongsTo(Ward::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
    public function post(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }

    public function review(): HasMany
    {
        return $this->HasMany(Review::class);
    }

    public function like(): HasMany
    {
        return $this->HasMany(Like::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'staff') {
            return str_ends_with($this->email, '@gamil.com') && $this->hasVerifiedEmail();
        }

        return true;
    }

    /**
     * model life cycle event listeners
     */

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
           // Thêm id người tạo
            if (Auth::check()) {
                $useradmin = Auth::user();
                $user->created_by = Auth::id();
                $user->shop_id = $useradmin->shop_id;
            }
        });

        static::created(function ($user) {
            // Đăng ký tài khoản shop
            if (!Auth::check() && Request::path() === 'livewire/update') {
                // Create Shop object and assign necessary values
                $shop = new Shop;
                $shop->name = $user->name;
                $shop->email = $user->email;
                $shop->status = 0;
                $shop->phone = $user->phone;
                $shop->description = $user->description;
                $shop->save();

                // Assign Shop ID to User and update User
                $user->shop_id = $shop->id;
                $user->save();

                // Assign User ID to Shop and update Shop
                $shop->user_id = $user->id;
                $shop->save();

                // Get all users with the 'super_admin' role
                $superAdmins = User::role('super_admin')->get();

                // Send email notification to all super_admins
                foreach ($superAdmins as $admin) {
                    Mail::to($admin->email)->send(new BrowseAdmin($user));
                }
                // Gửi thông báo tới người dùng mới được tạo
                Notification::make()
                    ->title('Bạn cần cập nhật thông tin của cửa hàng')
                    ->body('Bạn vào đây để cập nhật thông tin mới được đăng bán sản phẩm')
                    ->actions([
                        Action::make('Xem')
                            ->url(ShopResource::getUrl('edit', ['record' => $shop])),
                    ])
                    ->sendToDatabase($user);

                // Gửi thông báo tới các quản trị viên super_admin
                $superAdmins = User::role('super_admin')->get();
                foreach ($superAdmins as $admin) {
                    Notification::make()
                        ->title('Có một shop mới vừa đăng ký tài khoản')
                        ->icon('heroicon-o-home-modern')
                        ->body('Chờ bạn vào xét duyệt : ' . $shop->name)
                        ->sendToDatabase($admin);
                }
            }
        });
    }
    public function followerShop()
    {
        return $this->belongsToMany(Shop::class);
    }

    // My order
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function addOrderdatail(){
        return $this->hasMany(OrderDetail::class);
    }
    // lấy địa chỉ mặc định
    public function userAddressDefauld(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class)->where('is_default',1);
    }

    public function getUserAddressFormattedAttribute()
    {
        $address = $this->UserAddress;
        if (!$address) {
            return 'Địa chỉ không có sẵn';
        }

        return sprintf(
            '%s, %s, %s, %s',
            Str::ascii($address->address_specific),
            $address->ward ? Str::ascii($address->ward->name) : '',
            $address->district ? Str::ascii($address->district->name) : '',
            $address->province ? Str::ascii($address->province->name) : ''
        );
    }
}
