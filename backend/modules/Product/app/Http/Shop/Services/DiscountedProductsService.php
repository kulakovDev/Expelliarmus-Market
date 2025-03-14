<?php

declare(strict_types=1);

namespace Modules\Product\Http\Shop\Services;

use App\Services\Pagination\LimitOffsetDto;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Product\Models\Product;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Models\Discount;
use Modules\Warehouse\Models\ProductAttributeValue;
use Modules\Warehouse\Models\ProductVariation;

class DiscountedProductsService
{
    public function getFlashSalesPaginated(int $limit, int $offset): LimitOffsetDto
    {
        $discounts = Discount::query()
            ->with([
                'discountable' => function (MorphTo $morphTo) {
                    $columns = ['id', 'title', 'preview_image', 'product_article'];

                    $morphTo->morphWith([
                        ProductVariation::class => [
                            'product' => function ($query) use ($columns) {
                                $query->select(...$columns);
                            },
                        ],
                        ProductAttributeValue::class => [
                            'product' => function ($query) use ($columns) {
                                $query->select(...$columns);
                            },
                        ],
                    ]);

                    $morphTo->constrain([
                        Product::class => function ($query) use ($columns) {
                            $query->select(...$columns);
                        },
                    ]);
                },
            ])
            ->whereStatus(DiscountStatusEnum::ACTIVE)
            ->limit($limit)
            ->offset($offset)
            ->get();

        return new LimitOffsetDto(
            items: $discounts,
            total: Discount::query()->whereStatus(DiscountStatusEnum::ACTIVE)->count(),
            limit: $limit,
            offset: $offset,
        );
    }
}