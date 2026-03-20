<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $authorId = $admin ? $admin->id : 1;

        $articles = [
            // === TIN TỨC NỔI BẬT ===
            [
                'title' => 'iPhone 16 Pro Max lộ thiết kế mới: Viền siêu mỏng, nút Action thế hệ 2',
                'excerpt' => 'Apple dự kiến ra mắt iPhone 16 Pro Max vào tháng 9/2026 với nhiều cải tiến đáng giá bao gồm viền màn hình mỏng nhất từ trước đến nay.',
                'content' => '<h2>Thiết kế viền siêu mỏng</h2><p>Theo các nguồn tin rò rỉ từ chuỗi cung ứng, iPhone 16 Pro Max sẽ sở hữu viền màn hình mỏng nhất trong lịch sử iPhone. Apple đã phát triển công nghệ Border Reduction Structure (BRS) mới, giúp giảm viền xuống chỉ còn 1.2mm.</p><h2>Nút Action thế hệ 2</h2><p>Nút Action trên iPhone 16 Pro Max được nâng cấp với khả năng nhận diện cử chỉ vuốt, mang đến nhiều tùy chọn thao tác hơn. Người dùng có thể tùy chỉnh tối đa 6 hành động khác nhau.</p><h2>Camera 48MP cải tiến</h2><p>Hệ thống camera được nâng cấp với cảm biến mới, hỗ trợ quay video 8K và chụp ảnh ProRAW với chất lượng tốt hơn 20% so với thế hệ trước.</p>',
                'category' => 'news',
                'read_time' => '5 phút',
                'is_featured' => true,
                'thumbnail' => '/images/articles/iphone-16-leak.jpg',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'So sánh MacBook Air M3 vs MacBook Pro M3: Nên mua máy nào?',
                'excerpt' => 'Cùng chip M3 nhưng MacBook Air và MacBook Pro có nhiều khác biệt. Bài viết phân tích chi tiết giúp bạn chọn đúng máy phù hợp nhu cầu.',
                'content' => '<h2>Hiệu năng</h2><p>Cả hai đều sử dụng chip Apple M3, tuy nhiên MacBook Pro có hệ thống tản nhiệt chủ động với quạt, giúp duy trì hiệu năng cao trong thời gian dài hơn. MacBook Air với thiết kế fanless có thể bị throttle khi chạy tác vụ nặng liên tục.</p><h2>Màn hình</h2><p>MacBook Pro sở hữu màn hình Liquid Retina XDR với độ sáng tối đa 1600 nits, trong khi MacBook Air chỉ đạt 500 nits. Nếu bạn làm việc ngoài trời hoặc cần hiển thị HDR, Pro là lựa chọn tốt hơn.</p><h2>Pin</h2><p>MacBook Pro cho thời lượng pin lên tới 22 giờ, trong khi MacBook Air đạt khoảng 18 giờ.</p><h2>Kết luận</h2><p>MacBook Air M3 phù hợp cho sinh viên, dân văn phòng. MacBook Pro M3 dành cho creative professionals và developers.</p>',
                'category' => 'review',
                'read_time' => '8 phút',
                'is_featured' => true,
                'thumbnail' => '/images/articles/macbook-air-vs-pro.jpg',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Top 10 mẹo tiết kiệm pin iPhone cực hiệu quả 2026',
                'excerpt' => 'Tổng hợp 10 mẹo đơn giản nhưng hiệu quả giúp kéo dài thời lượng pin iPhone của bạn lên đến 30%.',
                'content' => '<h2>1. Tắt Background App Refresh</h2><p>Vào Settings > General > Background App Refresh và tắt các ứng dụng không cần thiết.</p><h2>2. Bật chế độ Dark Mode</h2><p>Với màn hình OLED, Dark Mode giúp tiết kiệm đáng kể pin vì các pixel đen không phát sáng.</p><h2>3. Giảm độ sáng tự động</h2><p>Kéo thanh độ sáng xuống mức vừa phải hoặc bật Auto-Brightness.</p><h2>4. Tắt Location Services không cần thiết</h2><p>Nhiều ứng dụng sử dụng GPS liên tục gây hao pin nhanh.</p><h2>5. Sử dụng Wi-Fi thay vì 5G</h2><p>Kết nối Wi-Fi tiêu thụ ít pin hơn so với mạng di động.</p>',
                'category' => 'tips',
                'read_time' => '4 phút',
                'is_featured' => true,
                'thumbnail' => '/images/articles/iphone-battery-tips.jpg',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Samsung Galaxy S25 Ultra chính thức ra mắt tại Việt Nam',
                'excerpt' => 'Samsung Galaxy S25 Ultra được bán chính hãng tại Việt Nam từ hôm nay với giá từ 33.990.000đ, đi kèm nhiều ưu đãi hấp dẫn.',
                'content' => '<h2>Giá bán và ưu đãi</h2><p>Galaxy S25 Ultra có 3 phiên bản bộ nhớ: 256GB (33.990.000đ), 512GB (37.990.000đ) và 1TB (41.990.000đ). Khách hàng đặt trước được tặng bộ phụ kiện trị giá 4.000.000đ.</p><h2>Điểm nổi bật</h2><p>S25 Ultra sở hữu chip Snapdragon 8 Elite, camera 200MP AI-enhanced, màn hình Dynamic AMOLED 2X và khung viền Titanium bền bỉ.</p><h2>Galaxy AI nâng cấp</h2><p>Tích hợp sâu Galaxy AI với khả năng dịch thuật real-time, chỉnh sửa ảnh AI và tóm tắt văn bản thông minh.</p>',
                'category' => 'news',
                'read_time' => '6 phút',
                'is_featured' => true,
                'thumbnail' => '/images/articles/galaxy-s25-ultra.jpg',
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Đánh giá ASUS ROG Strix G16 2024: Laptop gaming đáng mua nhất tầm giá',
                'excerpt' => 'ASUS ROG Strix G16 2024 gây ấn tượng với hiệu năng mạnh mẽ từ RTX 4060, màn hình 165Hz và mức giá hợp lý.',
                'content' => '<h2>Thiết kế</h2><p>ROG Strix G16 2024 có thiết kế góc cạnh đặc trưng của dòng ROG, với hệ thống đèn LED RGB Aura Sync trên nắp máy. Trọng lượng 2.3kg vẫn ở mức chấp nhận được cho laptop gaming 16 inch.</p><h2>Hiệu năng</h2><p>Intel Core i7-13650HX kết hợp RTX 4060 8GB cho hiệu năng gaming rất tốt. Hầu hết game AAA đều chạy mượt ở cài đặt High, 1080p với frame rate trên 60fps.</p><h2>Màn hình</h2><p>Panel IPS 16 inch, 165Hz, 100% sRGB. Màu sắc chính xác, tốc độ refresh cao phù hợp cho cả gaming và sáng tạo nội dung.</p><h2>Tổng kết: 8.5/10</h2><p>Một trong những laptop gaming đáng mua nhất trong tầm giá 30-35 triệu.</p>',
                'category' => 'review',
                'read_time' => '10 phút',
                'is_featured' => true,
                'thumbnail' => '/images/articles/rog-strix-g16-review.jpg',
                'published_at' => now()->subDays(5),
            ],

            // === TIN TỨC THƯỜNG ===
            [
                'title' => 'Xiaomi ra mắt Redmi Note 14 Pro với camera 200MP giá chỉ 7.990.000đ',
                'excerpt' => 'Redmi Note 14 Pro tiếp tục là smartphone tầm trung đáng giá với camera 200MP, sạc nhanh 120W và thiết kế cao cấp.',
                'content' => '<p>Xiaomi vừa chính thức ra mắt Redmi Note 14 Pro tại thị trường Việt Nam. Máy sở hữu camera chính 200MP Samsung ISOCELL HP3, chip MediaTek Dimensity 7300 Ultra và sạc nhanh HyperCharge 120W.</p>',
                'category' => 'news',
                'read_time' => '3 phút',
                'is_featured' => false,
                'thumbnail' => '/images/articles/redmi-note-14-pro.jpg',
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Hướng dẫn chọn mua laptop phù hợp cho sinh viên 2026',
                'excerpt' => 'Bài viết hướng dẫn chi tiết cách chọn laptop theo ngành học, ngân sách và nhu cầu sử dụng thực tế.',
                'content' => '<h2>Xác định nhu cầu theo ngành học</h2><p>Sinh viên CNTT cần máy cấu hình cao, RAM tối thiểu 16GB. Sinh viên kinh tế chỉ cần máy văn phòng nhẹ nhàng. Sinh viên thiết kế cần màn hình chất lượng cao.</p><h2>Ngân sách đề xuất</h2><p>10-15 triệu: Laptop văn phòng cơ bản. 15-25 triệu: Laptop tầm trung đa năng. 25-40 triệu: Laptop cao cấp chuyên nghiệp.</p>',
                'category' => 'tips',
                'read_time' => '7 phút',
                'is_featured' => false,
                'thumbnail' => '/images/articles/laptop-guide-student.jpg',
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'AirPods Pro 3 sẽ tích hợp cảm biến sức khỏe và chip H3',
                'excerpt' => 'Apple đang phát triển AirPods Pro thế hệ mới với khả năng đo nhịp tim, nhiệt độ cơ thể và chip xử lý âm thanh H3.',
                'content' => '<p>Theo báo cáo từ Bloomberg, AirPods Pro 3 sẽ là bước nhảy vọt lớn nhất của dòng tai nghe true wireless Apple. Ngoài cải tiến âm thanh với chip H3, tai nghe còn tích hợp các cảm biến sức khỏe tiên tiến.</p>',
                'category' => 'news',
                'read_time' => '4 phút',
                'is_featured' => false,
                'thumbnail' => '/images/articles/airpods-pro-3.jpg',
                'published_at' => now()->subDays(8),
            ],
        ];

        foreach ($articles as $data) {
            Article::create(array_merge($data, [
                'slug' => Str::slug($data['title']),
                'author_id' => $authorId,
                'view_count' => rand(100, 5000),
                'status' => 1,
            ]));
        }
    }
}
