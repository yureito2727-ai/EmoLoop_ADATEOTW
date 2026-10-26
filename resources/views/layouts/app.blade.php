<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Emo Loop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="@yield('body_class')">
    <header class="site-header">
        <nav class="site-nav">
            <div class="nav-left">
                <a href="{{ url('/shop') }}" class="pill pill-soft">SHOP</a>
                <a href="{{ url('/gioi-thieu') }}" class="pill pill-soft">Giới thiệu</a>
            </div>
            <div class="nav-right">
                @auth
                    <a href="{{ url('/gio-hang') }}" class="pill pill-yellow">
                        <i class="bi bi-cart3"></i> Giỏ hàng
                    </a>
                    <a href="{{ url('/tai-khoan') }}" class="avatar" title="{{ auth()->user()->name }}">AVT<br>USER</a>
                    <form method="POST" action="{{ url('/logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="pill pill-outline">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="pill pill-outline">Đăng nhập</a>
                    <a href="{{ url('/register') }}" class="pill pill-yellow">Đăng ký</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
    <div class="foot-grid">
        <div class="foot-about">
            <span class="foot-logo">Emo Loop</span>
            <p>Cửa hàng đồ lưu niệm kết hợp game visual novel. Chơi, sưu tầm và mang nhân vật về nhà.</p>
            <div class="foot-social">
                <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M13.5 22v-8.2h2.8l.5-3.3h-3.3V8.4c0-.9.4-1.7 1.8-1.7h1.6V3.8S15.6 3.5 14.3 3.5c-2.7 0-4.4 1.6-4.4 4.5v2.5H7.1v3.3h2.8V22h3.6z"/></svg></a>
                <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24"><path d="M12 7.3A4.7 4.7 0 1 0 12 16.7 4.7 4.7 0 0 0 12 7.3zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6zM17.2 6a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2zM12 3c-2.4 0-2.7 0-3.7.1-2.9.1-4.1 1.4-4.2 4.2C4 8.3 4 8.6 4 12s0 3.7.1 4.7c.1 2.8 1.4 4.1 4.2 4.2 1 .1 1.3.1 3.7.1s2.7 0 3.7-.1c2.8-.1 4.1-1.4 4.2-4.2.1-1 .1-1.3.1-4.7s0-3.7-.1-4.7c-.1-2.8-1.4-4.1-4.2-4.2C14.7 3 14.4 3 12 3zm0 1.8c2.4 0 2.7 0 3.6.1 2 .1 2.6 1 2.7 2.7.1 1 .1 1.2.1 4.4s0 3.4-.1 4.4c-.1 1.7-.7 2.6-2.7 2.7-.9 0-1.2.1-3.6.1s-2.7 0-3.6-.1c-2-.1-2.6-1-2.7-2.7-.1-1-.1-1.2-.1-4.4s0-3.4.1-4.4c.1-1.7.7-2.6 2.7-2.7.9-.1 1.2-.1 3.6-.1z"/></svg></a>
                <a href="#" aria-label="TikTok"><svg viewBox="0 0 24 24"><path d="M16.6 3h-3.1v12.2a2.6 2.6 0 1 1-2.6-2.6c.3 0 .5 0 .8.1V9.5a5.8 5.8 0 1 0 4.9 5.7V8.6a6.9 6.9 0 0 0 4 1.3V6.8A4 4 0 0 1 16.6 3z"/></svg></a>
                <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3L10 15z"/></svg></a>
            </div>
        </div>
        <div>
            <h3>Cửa hàng</h3>
            <ul>
                <li><a href="{{ url('/shop') }}">Tất cả sản phẩm</a></li>
                <li><a href="#">Móc khóa</a></li>
                <li><a href="#">Standee</a></li>
                <li><a href="#">Mã giảm giá</a></li>
            </ul>
        </div>
        <div>
            <h3>Hỗ trợ</h3>
            <ul>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Vận chuyển</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
            </ul>
        </div>
        <div>
            <h3>Liên hệ</h3>
            <ul>
                <li>hello@emoloop.test</li>
                <li>0900 000 000</li>
                <li>TP. Cần Thơ</li>
                <li><a href="#">Điều khoản sử dụng</a></li>
            </ul>
        </div>
    </div>
    <div class="foot-copy">
        <span>© 2026 Emo Loop. Bảo lưu mọi quyền.</span>
        <a href="#">Chính sách bảo mật</a>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>