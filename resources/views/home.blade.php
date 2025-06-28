@extends('layouts.admin.app')

@section('title', 'Home')

@section('content')
<!-- Home Section -->
<section id="home" class="slider-area hero-overly">
    <div class="slider-active">
        <div class="single-slider slider-height d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-9 col-md-10 col-sm-9">
                        <div class="hero__caption">
                            <h1 data-animation="fadeInLeft" data-delay="0.2s">Fast & Reliable Laundry Service</h1>
                            <p data-animation="fadeInLeft" data-delay="0.4s">Clean clothes, hassle-free.</p>
                            <a href="{{ route('pesan.create') }}" class="btn hero-btn">Buat Pesanan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services-area pt-top border-bottom pb-20 mb-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8">
                <div class="section-tittle text-center mb-55">
                    <span class="element">Our Process</span>
                    <h2>This is how we work</h2>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ([
                ['icon' => 'services-icon1.svg', 'title' => 'We collect your clothes'],
                ['icon' => 'services-icon2.svg', 'title' => 'Wash your clothes'],
                ['icon' => 'services-icon3.svg', 'title' => 'Get delivery'],
            ] as $step)
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center">
                    <div class="cat-icon">
                        <img src="{{ asset('assets/img/icon/' . $step['icon']) }}" alt="">
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#services">{{ $step['title'] }}</a></h5>
                        <p>The automated process starts as soon as your clothes go into the machine. The outcome is gleaming clothes!</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-area2 pb-bottom mt-30">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="about-img">
                    <img src="{{ asset('assets/img/gallery/about1.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="about-caption mb-50">
                    <div class="section-tittle mb-25">
                        <h2>About company</h2>
                    </div>
                    <p class="mb-20">The automated process starts as soon as your clothes go into the machine. The outcome is gleaming clothes!</p>
                    <p class="mb-30">The automated process starts as soon as your clothes go into the machine. The outcome is gleaming clothes!</p>
                    <a href="#about" class="btn">About Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact/Call to Action -->
<section id="contact" class="container">
    <section class="wantToWork-area" data-background="{{ asset('assets/img/gallery/section_bg01.png') }}">
        <div class="wants-wrapper w-padding2">
            <div class="row align-items-center justify-content-between">
                <div class="col-xl-8 col-lg-9 col-md-7">
                    <div class="wantToWork-caption wantToWork-caption2">
                        <h2>Call us for a service</h2>
                        <p>We deliver the goods to the most complicated places on earth</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-5">
                    <a href="#" class="btn wantToWork-btn"><img src="{{ asset('assets/img/icon/call2.png') }}" alt=""> Learn More</a>
                </div>
            </div>
        </div>
    </section>
</section>

<!-- Blog Anchor Placeholder -->
<section id="blog" class="section-padding40 text-center">
    <div class="container">
        <h2>Blog Section Placeholder</h2>
        <p>This section is for blog content linked from the navbar.</p>
    </div>
</section>
@endsection
