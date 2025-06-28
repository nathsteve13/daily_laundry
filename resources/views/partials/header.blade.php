<header>
    <div class="header-area">
        <div class="main-header header-sticky">
            <div class="header-left">
                <div class="logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/img/logo/logo_putih.jpg') }}" alt="" style="max-width: 80px; height: auto;">
                    </a>
                </div>
                <div class="menu-wrapper d-flex align-items-center">
                    <div class="main-menu d-none d-lg-block">
                        <nav>
                            <ul id="navigation">
                                <li class="active"><a href="#home">Home</a></li>
                                <li><a href="#about">About</a></li>
                                <li><a href="#services">Services</a></li>
                                <li><a href="#blog">Blog</a></li>
                                <li><a href="#contact">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="header-right d-none d-lg-block">
                <a href="https://wa.me/081510575807" target="_blank" class="header-btn1"><img src="{{ asset('assets/img/icon/call.png') }}" alt=""> 081510575807</a>
                <a href="{{route('pesan.create')}}" class="header-btn2">Buat Pesanan</a>
            </div>
            <div class="col-12">
                <div class="mobile_menu d-block d-lg-none"></div>
            </div>
        </div>
    </div>
</header>
