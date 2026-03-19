# PTSmart Backend — API

> Base URL: `http://127.0.0.1:8000/api`
> Auth Header: `Authorization: Bearer <token>`

---

# 🌐 PUBLIC — Không cần đăng nhập

## Auth

### 1. Đăng ký tài khoản
`POST /api/auth/register`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255 |
| `email` | ✅ | string | email, unique |
| `password` | ✅ | string | min:6, confirmed |
| `password_confirmation` | ✅ | string | trùng password |
| `phone` | ❌ | string | max:20 |

```json
{
  "name": "Nguyễn Văn A",
  "email": "nguyenvana@gmail.com",
  "password": "123123",
  "password_confirmation": "123123",
  "phone": "0901234567"
}
```

### 2. Đăng nhập
`POST /api/auth/login`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `email` | ✅ | string | email |
| `password` | ✅ | string | — |

```json
{
  "email": "admin@ptsmart.vn",
  "password": "123123"
}
```

---

## Home

### 3. Lấy dữ liệu trang chủ
`GET /api/home`

Không có params. Trả về: banners, categories, flash_sale, featured_products, new_products, best_sellers.

---

## Products

### 4. Danh sách sản phẩm
`GET /api/products`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `search` | ❌ | string | Tìm theo tên/mô tả |
| `category_id` | ❌ | integer | ID danh mục |
| `brand_id` | ❌ | integer | ID thương hiệu |
| `min_price` | ❌ | numeric | Giá tối thiểu |
| `max_price` | ❌ | numeric | Giá tối đa |
| `sort` | ❌ | string | `price_asc` · `price_desc` · `newest` · `best_selling` |
| `per_page` | ❌ | integer | default:20, max:50 |

```
GET /api/products?search=iphone&category_id=1&min_price=10000000&sort=price_asc&per_page=10
```

### 5. Sản phẩm nổi bật
`GET /api/products/featured`

Không có params. Trả về tối đa 12 SP.

### 6. Chi tiết sản phẩm
`GET /api/products/{product}`

```
GET /api/products/1
```

### 7. Đánh giá của sản phẩm
`GET /api/products/{product}/reviews`

```
GET /api/products/1/reviews
```

Phân trang 10/page.

---

## Categories

### 8. Danh sách danh mục
`GET /api/categories`

Không có params.

### 9. Sản phẩm theo danh mục
`GET /api/categories/{slug}/products`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `slug` | ✅ (url) | string | Slug danh mục |
| `sort` | ❌ | string | `price_asc` · `price_desc` · `newest` · `best_selling` |
| `per_page` | ❌ | integer | default:20, max:50 |

```
GET /api/categories/dien-thoai/products?sort=newest&per_page=12
```

---

## Brands

### 10. Danh sách thương hiệu
`GET /api/brands`

Không có params.

---

## Flash Sales

### 11. Flash sale đang diễn ra
`GET /api/flash-sales/active`

Không có params.

### 12. Flash sale sắp tới
`GET /api/flash-sales/upcoming`

Không có params. Tối đa 5 flash sale.

---

## Promotions

### 13. Danh sách khuyến mãi
`GET /api/promotions`

Không có params.

---

## Articles

### 14. Danh sách bài viết
`GET /api/articles`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `category` | ❌ | string | Lọc theo chuyên mục |

```
GET /api/articles?category=tin-tuc
```

### 15. Bài viết nổi bật
`GET /api/articles/featured`

Không có params. Tối đa 5 bài.

### 16. Chi tiết bài viết
`GET /api/articles/{slug}`

```
GET /api/articles/huong-dan-mua-hang-online-ab1c2
```

---

## Contact & Branches

### 17. Gửi liên hệ
`POST /api/contact`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255 |
| `email` | ✅ | string | email, max:255 |
| `phone` | ❌ | string | max:20 |
| `message` | ✅ | string | — |

```json
{
  "name": "Nguyễn Văn A",
  "email": "nguyenvana@gmail.com",
  "phone": "0901234567",
  "message": "Tôi muốn hỏi về sản phẩm iPhone 15"
}
```

### 18. Danh sách chi nhánh
`GET /api/branches`

Không có params.

---

## Payment Callbacks (Webhook)

### 19. Callback VNPay
`GET /api/payments/vnpay/callback` — Webhook tự động, không gọi thủ công.

### 20. Callback MoMo
`POST /api/payments/momo/callback` — Webhook tự động, không gọi thủ công.

---

# 🔒 AUTHENTICATED — Cần Bearer Token

## Auth

### 21. Đăng xuất
`POST /api/auth/logout`

Không có params.

### 22. Lấy thông tin tài khoản
`GET /api/auth/me`

Không có params. Trả về user + danh sách địa chỉ.

---

## Profile

### 23. Cập nhật hồ sơ
`PUT /api/profile`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ❌ | string | max:255 |
| `phone` | ❌ | string | max:20 |
| `birthday` | ❌ | string | YYYY-MM-DD |
| `gender` | ❌ | string | `male` · `female` · `other` |
| `city` | ❌ | string | max:100 |

```json
{
  "name": "Lê Hoàng Phong",
  "phone": "0379727659",
  "birthday": "2000-01-15",
  "gender": "male",
  "city": "TP.HCM"
}
```

### 24. Tải ảnh đại diện
`POST /api/profile/avatar` · `multipart/form-data`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `avatar` | ✅ | file | jpg/jpeg/png/webp, max:2048KB |

> Postman: Body → form-data → key `avatar` (chọn File) → chọn ảnh.

### 25. Đổi mật khẩu
`PUT /api/profile/password`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `current_password` | ✅ | string | Mật khẩu hiện tại |
| `password` | ✅ | string | min:6 |
| `password_confirmation` | ✅ | string | trùng password |

```json
{
  "current_password": "123123",
  "password": "newpass123",
  "password_confirmation": "newpass123"
}
```

---

## Addresses

### 26. Danh sách địa chỉ
`GET /api/addresses`

Không có params.

### 27. Thêm địa chỉ
`POST /api/addresses`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255, tên người nhận |
| `phone` | ✅ | string | max:20 |
| `address` | ✅ | string | Địa chỉ chi tiết |
| `type` | ❌ | string | `home` · `office` · `other` |
| `is_default` | ❌ | boolean | true/false |

```json
{
  "name": "Lê Hoàng Phong",
  "phone": "0379727659",
  "address": "123 Nguyễn Huệ, Quận 1, TP.HCM",
  "type": "home",
  "is_default": true
}
```

### 28. Sửa địa chỉ
`PUT /api/addresses/{address}`

```json
// PUT /api/addresses/1
{
  "name": "Phong Hoàng",
  "phone": "0912345678",
  "address": "456 Lê Lợi, Quận 3, TP.HCM",
  "type": "office"
}
```

### 29. Xóa địa chỉ
`DELETE /api/addresses/{address}`

```
DELETE /api/addresses/1
```

### 30. Đặt địa chỉ mặc định
`PUT /api/addresses/{address}/default`

```
PUT /api/addresses/2/default
```

---

## Cart

### 31. Xem giỏ hàng
`GET /api/cart`

Không có params.

### 32. Thêm sản phẩm vào giỏ
`POST /api/cart/items`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `product_id` | ✅ | integer | ID sản phẩm |
| `variant_id` | ❌ | integer | ID biến thể |
| `quantity` | ❌ | integer | min:1, default:1 |

```json
{
  "product_id": 1,
  "variant_id": null,
  "quantity": 2
}
```

### 33. Cập nhật số lượng
`PUT /api/cart/items/{item}`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `quantity` | ✅ | integer | min:1 |

```json
// PUT /api/cart/items/1
{
  "quantity": 3
}
```

### 34. Xóa sản phẩm khỏi giỏ
`DELETE /api/cart/items/{item}`

```
DELETE /api/cart/items/1
```

### 35. Xóa toàn bộ giỏ hàng
`DELETE /api/cart/clear`

Không có params.

---

## Orders

### 36. Đặt hàng
`POST /api/orders`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `address_id` | ✅ | integer | ID địa chỉ |
| `payment_method` | ✅ | string | `cod` · [vnpay](file:///c:/Project/ptsmart-be/app/Http/Controllers/Api/PaymentController.php#59-98) · [momo](file:///c:/Project/ptsmart-be/app/Http/Controllers/Api/PaymentController.php#141-168) |
| `coupon_code` | ❌ | string | Mã giảm giá |
| `note` | ❌ | string | Ghi chú |

```json
{
  "address_id": 1,
  "payment_method": "cod",
  "coupon_code": "PTSMART50",
  "note": "Giao giờ hành chính"
}
```

### 37. Danh sách đơn hàng
`GET /api/orders`

Không có params. Phân trang 10/page.

### 38. Chi tiết đơn hàng
`GET /api/orders/{id}`

```
GET /api/orders/1
```

### 39. Hủy đơn hàng
`PUT /api/orders/{id}/cancel`

```
PUT /api/orders/1/cancel
```

---

## Payments

### 40. Tạo thanh toán VNPay
`POST /api/payments/vnpay/create`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `order_id` | ✅ | integer | Đơn hàng phải dùng VNPay |

```json
{
  "order_id": 1
}
```

### 41. Tạo thanh toán MoMo
`POST /api/payments/momo/create`

```json
{
  "order_id": 1
}
```

---

## Payment Methods

### 42. Danh sách phương thức TT
`GET /api/payment-methods`

Không có params.

### 43. Thêm phương thức TT
`POST /api/payment-methods`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `type` | ✅ | string | `card` · `ewallet` |
| `provider` | ✅ | string | max:50 |
| `display_name` | ✅ | string | max:100 |
| `masked_number` | ❌ | string | max:50 |
| `card_holder` | ❌ | string | max:255 |
| `expiry` | ❌ | string | max:10 |
| `is_default` | ❌ | boolean | true/false |
| `metadata` | ❌ | object | JSON bổ sung |

```json
{
  "type": "card",
  "provider": "visa",
  "display_name": "Visa **** 1234",
  "masked_number": "**** **** **** 1234",
  "card_holder": "LE HOANG PHONG",
  "expiry": "12/28",
  "is_default": true,
  "metadata": { "bank": "Vietcombank" }
}
```

### 44. Xóa phương thức TT
`DELETE /api/payment-methods/{paymentMethod}`

```
DELETE /api/payment-methods/1
```

### 45. Đặt PT thanh toán mặc định
`PUT /api/payment-methods/{paymentMethod}/default`

```
PUT /api/payment-methods/2/default
```

---

## Wishlist

### 46. Danh sách yêu thích
`GET /api/wishlist`

Không có params.

### 47. Thêm vào yêu thích
`POST /api/wishlist`

```json
{
  "product_id": 5
}
```

### 48. Xóa khỏi yêu thích
`DELETE /api/wishlist/{productId}`

```
DELETE /api/wishlist/5
```

---

## Reviews

### 49. Gửi đánh giá sản phẩm
`POST /api/reviews`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `product_id` | ✅ | integer | SP phải thuộc đơn hàng |
| `order_id` | ✅ | integer | Đơn phải `completed` |
| `rating` | ✅ | integer | 1 → 5 |
| `comment` | ❌ | string | Nội dung |

```json
{
  "product_id": 1,
  "order_id": 1,
  "rating": 5,
  "comment": "Sản phẩm rất tốt, giao hàng nhanh!"
}
```

---

## Coupons

### 50. Mã giảm giá của tôi
`GET /api/coupons/my`

Không có params.

### 51. Áp dụng mã giảm giá
`POST /api/coupons/apply`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `code` | ✅ | string | Mã coupon |
| `subtotal` | ✅ | numeric | Tổng tiền đơn, min:0 |

```json
{
  "code": "PTSMART50",
  "subtotal": 1500000
}
```

---

# 🛡️ ADMIN — Cần Bearer Token + Role Admin

## Dashboard

### 52. Thống kê tổng quan
`GET /api/admin/dashboard/stats`

Không có params.

### 53. Biểu đồ doanh thu
`GET /api/admin/dashboard/revenue-chart`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `period` | ❌ | string | `daily` · `weekly` · `monthly` |

```
GET /api/admin/dashboard/revenue-chart?period=monthly
```

### 54. Đơn hàng gần đây
`GET /api/admin/dashboard/recent-orders`

Không có params. Trả về 10 đơn mới nhất.

---

## Quản lý sản phẩm

### 55. DS sản phẩm (admin)
`GET /api/admin/products`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `search` | ❌ | string | Tìm theo tên |
| `status` | ❌ | integer | `0` (ẩn) · `1` (hiện) |
| `category_id` | ❌ | integer | Lọc danh mục |

```
GET /api/admin/products?search=iphone&status=1&category_id=1
```

### 56. Tạo sản phẩm
`POST /api/admin/products` · `multipart/form-data`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255 |
| `description` | ❌ | string | Mô tả |
| `specifications` | ❌ | array | Thông số KT |
| `category_id` | ✅ | integer | ID danh mục |
| `brand_id` | ✅ | integer | ID thương hiệu |
| `price` | ✅ | numeric | min:0, giá gốc |
| `sale_price` | ❌ | numeric | Giá KM |
| `stock` | ✅ | integer | min:0, tồn kho |
| `is_featured` | ❌ | boolean | Nổi bật |
| `status` | ❌ | integer | `0` · `1` |
| `thumbnail` | ❌ | file | image, max:2048KB |
| `images[]` | ❌ | file[] | Mảng ảnh |
| `variants[i][name]` | ❌ | string | Tên biến thể |
| `variants[i][sku]` | ❌ | string | Mã SKU |
| `variants[i][price]` | ❌ | numeric | Giá biến thể |
| `variants[i][stock]` | ❌ | integer | Tồn kho biến thể |
| `variants[i][attributes]` | ❌ | object | VD: `{"color":"Đen"}` |

```
name              = "iPhone 15 Pro Max 256GB"
description       = "Chip A17 Pro, Camera 48MP"
category_id       = 1
brand_id          = 1
price             = 34990000
sale_price        = 32990000
stock             = 50
is_featured       = true
status            = 1
thumbnail         = [Chọn file ảnh]
images[0]         = [Chọn file ảnh]
variants[0][name] = "Đen Titan 256GB"
variants[0][sku]  = "IP15PM-BK-256"
variants[0][price]= 34990000
variants[0][stock]= 20
```

### 57. Cập nhật sản phẩm
`PUT /api/admin/products/{product}` · `multipart/form-data`

Giống #56, tất cả optional.

```
PUT /api/admin/products/1
name  = "iPhone 15 Pro Max 512GB"
price = 39990000
```

### 58. Xóa sản phẩm
`DELETE /api/admin/products/{product}`

```
DELETE /api/admin/products/1
```

### 59. Upload ảnh sản phẩm
`POST /api/admin/products/{product}/images` · `multipart/form-data`

```
POST /api/admin/products/1/images
images[0] = [Chọn file ảnh]
images[1] = [Chọn file ảnh]
```

---

## Quản lý danh mục

### 60. DS danh mục (admin)
`GET /api/admin/categories`

Không có params.

### 61. Tạo danh mục
`POST /api/admin/categories`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255 |
| `icon` | ❌ | string | max:100 |
| `parent_id` | ❌ | integer | ID danh mục cha |
| `sort_order` | ❌ | integer | Thứ tự |
| `status` | ❌ | integer | `0` · `1` |

```json
{
  "name": "Điện thoại",
  "icon": "icon-phone",
  "parent_id": null,
  "sort_order": 1,
  "status": 1
}
```

### 62. Cập nhật danh mục
`PUT /api/admin/categories/{category}`

```json
// PUT /api/admin/categories/1
{
  "name": "Smartphone",
  "sort_order": 2
}
```

### 63. Xóa danh mục
`DELETE /api/admin/categories/{category}`

```
DELETE /api/admin/categories/1
```

> Lỗi 422 nếu còn sản phẩm.

---

## Quản lý thương hiệu

### 64. DS thương hiệu (admin)
`GET /api/admin/brands`

Không có params.

### 65. Tạo thương hiệu
`POST /api/admin/brands` · `multipart/form-data`

```
name = "Samsung"
logo = [Chọn file ảnh]
```

### 66. Cập nhật thương hiệu
`PUT /api/admin/brands/{brand}` · `multipart/form-data`

```
PUT /api/admin/brands/1
name = "Samsung Electronics"
logo = [Chọn file ảnh mới]
```

### 67. Xóa thương hiệu
`DELETE /api/admin/brands/{brand}`

```
DELETE /api/admin/brands/1
```

---

## Quản lý đơn hàng

### 68. DS đơn hàng (admin)
`GET /api/admin/orders`

| Field | Bắt buộc | Kiểu | Mô tả |
|-------|----------|------|-------|
| `status` | ❌ | string | `pending` · `confirmed` · `shipping` · `completed` · `canceled` |

```
GET /api/admin/orders?status=pending
```

### 69. Chi tiết đơn hàng (admin)
`GET /api/admin/orders/{order}`

```
GET /api/admin/orders/1
```

### 70. Cập nhật trạng thái đơn
`PUT /api/admin/orders/{order}/status`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `status` | ✅ | string | `pending` · `confirmed` · `shipping` · `completed` · `canceled` |

```json
// PUT /api/admin/orders/1/status
{
  "status": "confirmed"
}
```

---

## Quản lý người dùng

### 71. DS người dùng
`GET /api/admin/users`

```
GET /api/admin/users?search=phong
```

### 72. Cập nhật người dùng
`PUT /api/admin/users/{user}`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `status` | ❌ | integer | `0` (khóa) · `1` (mở) |
| `role` | ❌ | string | `admin` · `customer` |

```json
// PUT /api/admin/users/2
{
  "status": 1,
  "role": "admin"
}
```

---

## Quản lý mã giảm giá

### 73. DS mã giảm giá
`GET /api/admin/coupons`

Không có params.

### 74. Tạo mã giảm giá
`POST /api/admin/coupons`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `code` | ✅ | string | max:50, unique |
| `title` | ✅ | string | max:255 |
| `description` | ❌ | string | — |
| `type` | ✅ | string | `fixed` · `percent` · `shipping` |
| `discount_value` | ✅ | numeric | min:0 |
| `min_order` | ❌ | numeric | Đơn tối thiểu |
| `max_discount` | ❌ | numeric | Giảm tối đa |
| `usage_limit` | ❌ | integer | Số lần dùng |
| `expired_at` | ✅ | string | date, sau hiện tại |
| `category` | ❌ | string | max:50 |
| `status` | ❌ | integer | `0` · `1` |

```json
{
  "code": "SUMMER2026",
  "title": "Giảm 15% mùa hè",
  "description": "Giảm 15% tối đa 300.000đ cho đơn từ 1.000.000đ",
  "type": "percent",
  "discount_value": 15,
  "min_order": 1000000,
  "max_discount": 300000,
  "usage_limit": 100,
  "expired_at": "2026-08-31",
  "category": "shopping",
  "status": 1
}
```

### 75. Cập nhật mã giảm giá
`PUT /api/admin/coupons/{coupon}`

```json
// PUT /api/admin/coupons/1
{
  "title": "Giảm 20% mùa hè",
  "discount_value": 20
}
```

### 76. Xóa mã giảm giá
`DELETE /api/admin/coupons/{coupon}`

```
DELETE /api/admin/coupons/1
```

---

## Quản lý Flash Sale

### 77. DS flash sale
`GET /api/admin/flash-sales`

Không có params.

### 78. Tạo flash sale
`POST /api/admin/flash-sales`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `title` | ✅ | string | max:255 |
| `start_at` | ✅ | string | date |
| `end_at` | ✅ | string | date, sau start_at |
| `status` | ❌ | integer | `0` · `1` |
| `items[i][product_id]` | ✅ | integer | ID sản phẩm |
| `items[i][flash_price]` | ✅ | numeric | Giá flash sale |
| `items[i][quantity]` | ✅ | integer | Số lượng bán |

```json
{
  "title": "Flash Sale Thứ 6",
  "start_at": "2026-04-01 09:00:00",
  "end_at": "2026-04-01 15:00:00",
  "status": 1,
  "items": [
    { "product_id": 1, "flash_price": 25990000, "quantity": 20 },
    { "product_id": 3, "flash_price": 8990000, "quantity": 50 }
  ]
}
```

### 79. Cập nhật flash sale
`PUT /api/admin/flash-sales/{flashSale}`

```json
// PUT /api/admin/flash-sales/1
{
  "title": "Flash Sale Cuối Tuần",
  "end_at": "2026-04-01 21:00:00"
}
```

### 80. Xóa flash sale
`DELETE /api/admin/flash-sales/{flashSale}`

```
DELETE /api/admin/flash-sales/1
```

---

## Quản lý bài viết

### 81. DS bài viết (admin)
`GET /api/admin/articles`

```
GET /api/admin/articles?category=tin-tuc
```

### 82. Tạo bài viết
`POST /api/admin/articles` · `multipart/form-data`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `title` | ✅ | string | max:255 |
| `content` | ✅ | string | Nội dung HTML |
| `excerpt` | ❌ | string | Tóm tắt |
| `thumbnail` | ❌ | file | image, max:2048KB |
| `category` | ❌ | string | max:50 |
| `read_time` | ❌ | string | max:20 |
| `is_featured` | ❌ | boolean | — |
| `status` | ❌ | integer | `0` (nháp) · `1` (xuất bản) |
| `published_at` | ❌ | string | date |

```
title        = "Hướng dẫn chọn laptop gaming 2026"
content      = "<p>Bài viết hướng dẫn...</p>"
excerpt      = "Top laptop gaming đáng mua nhất 2026"
thumbnail    = [Chọn file ảnh]
category     = "huong-dan"
read_time    = "5 phút"
is_featured  = true
status       = 1
published_at = "2026-04-01"
```

### 83. Cập nhật bài viết
`PUT /api/admin/articles/{article}` · `multipart/form-data`

Giống #82, tất cả optional.

### 84. Xóa bài viết
`DELETE /api/admin/articles/{article}`

```
DELETE /api/admin/articles/1
```

---

## Quản lý banner

### 85. DS banner
`GET /api/admin/banners`

Không có params.

### 86. Tạo banner
`POST /api/admin/banners` · `multipart/form-data`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `title` | ✅ | string | max:255 |
| `subtitle` | ❌ | string | Phụ đề |
| `image` | ✅ | file | image, max:2048KB |
| `link` | ❌ | string | max:500 |
| `position` | ❌ | string | max:50 |
| `sort_order` | ❌ | integer | Thứ tự |
| `status` | ❌ | integer | `0` · `1` |

```
title      = "Khuyến mãi tháng 4"
subtitle   = "Giảm đến 50%"
image      = [Chọn file ảnh]
link       = "/promotions/thang-4"
position   = "home_hero"
sort_order = 1
status     = 1
```

### 87. Cập nhật banner
`PUT /api/admin/banners/{banner}` · `multipart/form-data`

Giống #86, tất cả optional.

### 88. Xóa banner
`DELETE /api/admin/banners/{banner}`

```
DELETE /api/admin/banners/1
```

---

## Quản lý khuyến mãi

### 89. DS khuyến mãi (admin)
`GET /api/admin/promotions`

Không có params.

### 90. Tạo khuyến mãi
`POST /api/admin/promotions` · `multipart/form-data`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `title` | ✅ | string | max:255 |
| `description` | ❌ | string | — |
| `image` | ❌ | file | image, max:2048KB |
| `type` | ❌ | string | max:50 |
| `start_at` | ✅ | string | date |
| `end_at` | ✅ | string | date, sau start_at |
| `status` | ❌ | integer | `0` · `1` |
| `product_ids[]` | ❌ | integer[] | Mảng ID SP |

```
title          = "Tuần lễ Samsung"
description    = "Giảm giá toàn bộ SP Samsung"
image          = [Chọn file ảnh]
type           = "brand_sale"
start_at       = "2026-04-01"
end_at         = "2026-04-07"
status         = 1
product_ids[0] = 2
product_ids[1] = 5
```

### 91. Cập nhật khuyến mãi
`PUT /api/admin/promotions/{promotion}` · `multipart/form-data`

Giống #90, tất cả optional.

### 92. Xóa khuyến mãi
`DELETE /api/admin/promotions/{promotion}`

```
DELETE /api/admin/promotions/1
```

---

## Quản lý liên hệ

### 93. DS liên hệ
`GET /api/admin/contacts`

Không có params. Phân trang 20/page.

### 94. Đánh dấu đã đọc
`PUT /api/admin/contacts/{contact}/read`

```
PUT /api/admin/contacts/1/read
```

---

## Quản lý chi nhánh

### 95. DS chi nhánh (admin)
`GET /api/admin/branches`

Không có params.

### 96. Tạo chi nhánh
`POST /api/admin/branches`

| Field | Bắt buộc | Kiểu | Validation |
|-------|----------|------|------------|
| `name` | ✅ | string | max:255 |
| `address` | ✅ | string | — |
| `phone` | ❌ | string | max:20 |
| `lat` | ❌ | numeric | Vĩ độ |
| `lng` | ❌ | numeric | Kinh độ |

```json
{
  "name": "PTSmart Cần Thơ",
  "address": "99 Nguyễn Trãi, Ninh Kiều, Cần Thơ",
  "phone": "0292 123 4567",
  "lat": 10.0452,
  "lng": 105.7469
}
```

### 97. Cập nhật chi nhánh
`PUT /api/admin/branches/{branch}`

```json
// PUT /api/admin/branches/1
{
  "name": "PTSmart Hà Nội - Cầu Giấy",
  "phone": "024 9999 8888"
}
```

### 98. Xóa chi nhánh
`DELETE /api/admin/branches/{branch}`

```
DELETE /api/admin/branches/1
```

---

**Tổng: 98 endpoints** · 20 Công khai · 31 Cần đăng nhập · 47 Quản trị
