<?php

namespace App\Domain\Admin_Panel\Coupon\Data;

/**
 * DTO.
 */
final class CouponDataRead
{
    public ?int $id = null;

    public ?string $coupon_title = null;

    public ?string $coupon_code = null;

    public ?string $valid_from_date	 = null;

    public ?string $valid_to_date = null;

    public ?string $bonus_type = null;

    public ?string $bonus_value = null;

    public ?int $max_price = null;

    public ?string $reusable = null;

    public ?string $created_at = null;

}
