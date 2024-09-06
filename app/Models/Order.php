<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Mail\OrderCancelledMail;
use App\Mail\OrderOnHoldMail;
use App\Mail\OrderProcessingMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

// Import Log facade
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_address_id',
        'delivery_date',
        'total_price',
        'shipping_unit',
        'user_id',
        'voucher_id',
        'status',
        'payment_method_id',
        'code',
        'created_at',
        'updated_at',
        'shop_id',
        'cancel_reason',
        'on_hold',
        'is_paid',
        'day_paid',
        'lading_code',
        'check_order_shop',
        'canceled_by'
    ];
    protected $casts = [
        'status' => OrderStatus::class, // Cast status field to OrderStatus enum
    ];

    public function ShippingAddress(): BelongsTo
    {
        return $this->BelongsTo(ShippingAddress::class);
    }

    public function UserAddress(): BelongsTo
    {
        return $this->BelongsTo(UserAddress::class);
    }

    public function User(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

    public function Voucher(): BelongsTo
    {
        return $this->BelongsTo(Voucher::class);
    }

    public function PaymentMethod(): BelongsTo
    {
        return $this->BelongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_detail_id');
    }

    public function OrderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }


    protected static function booted()
    {
        parent::booted();

        // Sự kiện được gọi sau khi model đã được lưu
        static::saved(function ($order) {
//            if (Request::path() === 'livewire/update') {
//                if ($order->status == OrderStatus::Cancelled) {
//                    Mail::to($order->user->email)->send(new OrderCancelledMail($order, $order->cancel_reason));
//                }
//            }
//            if ($order->is_paid == 1) {
//                $order->day_paid = now();
//                $order->save();
//            }
        });

    }



    //self::New => 'Mới',
    //self::Processing => 'Đang xử lý',
    //self::Shipped => 'Đã vận chuyển',
    //self::Delivered => 'Đã giao hàng',
    //self::Cancelled => 'Đã hủy bỏ',
    //self::OnHold => 'Đơn tạm giữ',
    // Các phương thức đếm các số lượng trạng thái đơn hàng
    // + Chờ xử lý
    // + Chờ giao hàng

    public static function processingStatus()
    {
        return self::where('status', 'Đang xử lý')->count();
    }
    // + Vận chuyển
    public static function shippedStatus()
    {
        return self::where('status', 'Đã vận chuyển')->count();
    }
    // + Hoàn thành
    public static function deliveredStatus()
    {
        return self::where('status', 'Đã giao hàng')->count();
    }
    // + chờ lấy hàng hiện trong shop
    public static function waitingdelivery(){
        return self::where('status', 'Chờ lấy hàng')->count();
    }
    //
    public static function NotProcessed(){
        return self::where('status', 'Chưa xử lý')->count();
    }
    public static function processedShop(){
        return self::where('check_order_shop', 1)->where('status','!=' ,'Chưa xử lý')->where('status','!=' ,'Đã vận chuyển')->count();
    }

    public static function getOrderStatusOptions(): array
    {
        return array_reduce(OrderStatus::cases(), function ($options, $status) {
            $options[$status->value] = $status->value;
            return $options;
        }, []);
    }
    // lấy địa chỉ dưới dạng chuỗi để hiển thị ra shop
    public function getUserAddressFormattedAttribute()
    {
        $address = $this->UserAddress;
        if (!$address) {
            return 'Địa chỉ không có sẵn';
        }

        return sprintf(
            '%s, %s, %s, %s',
            $address->address_specific,
            $address->ward ? $address->ward->name : '',
            $address->district ? $address->district->name : '',
            $address->province ? $address->province->name : ''
        );
    }
    // tính số ngày mà shop không vào nhận đơn
    public function getDaysSinceCustomTimeAttribute()
    {
        if ($this->custom_time) {
            $customTime = Carbon::parse($this->custom_time)->startOfDay();
            $now = Carbon::now()->startOfDay();

            return $now->diffInDays($customTime) . ' Ngày';
        }

        return 'không có';
    }


}
