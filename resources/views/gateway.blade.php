@extends('layouts.gateway_layout')
@section('main_content')
<!-- load a Bengali-capable webfont (optional but recommended) -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif&display=swap" rel="stylesheet">

<style>
 
  .arc-text {
    font-family: "Noto Serif", "Noto Sans", sans-serif;
    font-weight: 700;
    font-size: 47px;           
    fill: #111;               
    stroke: #111;            
    stroke-width: 0.4;
    letter-spacing: -4px;        
  }

  .logo-centered {
    width: 120px;               
    height: 120px;
    object-fit: contain;
    display: block;
    margin: -150px auto 0;       
    border-radius: 50%;
    background: transparent;
  }

  /* container spacing */
  .arch-wrap {  margin-bottom: 10px; }
</style>

    <div class="card shadow border-0 rounded-4 mb-5" style="background: floralwhite;">
        <div class="card-body">

            <div class="row justify-content-center arch-wrap">
                <div class="col-md-8 text-center">

                    <!-- SVG: an elliptical arc and text on the arc -->
                    <svg viewBox="0 0 600 260" width="100%" height="355" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="arched title">
                    <defs>
                        <!-- 
                        Path is an elliptical arc from left to right.
                        Change the numbers (rx, ry) to adjust the curvature:
                            M x1,y A rx,ry 0 0,1 x2,y
                        -->
                        <path id="arcTop" d="M 80,200 A 250,140 0 0,1 520,200" fill="transparent"/>
                    </defs>

                    <!--
                        textLength controls how long the text occupies on the path.
                        lengthAdjust="spacingAndGlyphs" stretches spacing/glyphs to fill that length.
                        startOffset="50%" centers the text on the path.
                    -->
                    <text text-anchor="middle" dominant-baseline="middle">
                        <textPath href="#arcTop"
                                startOffset="50%"
                                textLength="440"
                                lengthAdjust="spacingAndGlyphs"
                                class="arc-text">
                        ঢাকা ক্যান্টনমেন্ট বোর্ড
                        </textPath>
                    </text>
                    </svg>

                    <!-- logo: negative margin pulls it up so the arc appears to wrap around it -->
                    <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo-centered">

                    <!-- subtitle -->
                    <h3 class="fw-bold mt-2">পানি ও বিদ্যুৎ বিল</h3>
                </div>
            </div>

            <div class="row justify-content-center" style="margin-bottom: 6rem">

                {{-- Electricity Card --}}
                <div class="col-md-3 mt-4">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-body text-center p-5">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-bolt fa-3x"></i>
                            </div>
                            <h4 class="fw-bold mb-3"></h4>
                            <a href="{{ url('/electricity/login') }}" class="btn btn-warning btn-lg w-100 rounded-pill shadow-sm">
                                বিদ্যুৎ বিল
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Water Card --}}
                <div class="col-md-3 mt-4">
                    <div class="card shadow border-0 rounded-4">
                        <div class="card-body text-center p-5">
                            <div class="mb-3 text-primary">
                                <i class="fas fa-tint fa-3x"></i>
                            </div>
                            <h4 class="fw-bold mb-3"></h4>
                            <a href="{{ url('/water/login') }}" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                                পানি বিল
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection