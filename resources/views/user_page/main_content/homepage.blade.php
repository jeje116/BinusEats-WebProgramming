@extends('/user_page.main_template')

@section('content')
    <div class="topup-history">

        <div class="emoney_section">
            <div class="topup_carousel_block">
                <div id="topup-carousel" class="carousel slide">
                    <ol class="carousel-indicators">
                        <li data-bs-target="#topup-carousel" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#topup-carousel" data-bs-slide-to="1"></li>
                        <li data-bs-target="#topup-carousel" data-bs-slide-to="2"></li>
                    </ol>

                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="topup">
                                {{-- @foreach ($emoneys as $emoney) --}}
                                <div class="bipay_home" id="emoney">
                                    <div class="method">
                                        <img style="width: 1.7vw; height:max-content; display:flex; align-self: center;" src="{{ $emoneys[0]->img }}" alt="bipay">
                                        <p style="padding-left: 0.5vw;"> {{ $emoneys[0]->name }}</p>
                                    </div>
                                    <p style="margin-right: 1vw">Rp{{number_format($moneys[$emoneys[0]->id-1]->totalAmount, 0 , '.' , '.' )}}</p>
                                </div>

                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="topup">
                                <div class="OVO_home" id="emoney">
                                    <div class="method">
                                        <img style="width: 1.7vw; height:max-content; display:flex; align-self: center;" src="{{ $emoneys[1]->img }}" alt="bipay">
                                        <p style="padding-left: 0.5vw;"> {{ $emoneys[1]->name }}</p>
                                    </div>
                                    <p style="margin-right: 1vw">Rp{{number_format($moneys[$emoneys[1]->id-1]->totalAmount, 0 , '.' , '.' )}}</p>
                                </div>

                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="topup">
                                <div class="GoPay_home" id="emoney">
                                    <div class="method">
                                        <img style="width: 1.7vw; height:max-content; display:flex; align-self: center;" src="{{ $emoneys[2]->img }}" alt="bipay">
                                        <p style="padding-left: 0.5vw;"> {{ $emoneys[2]->name }}</p>
                                    </div>
                                    <p style="margin-right: 1vw">Rp{{number_format($moneys[$emoneys[2]->id-1]->totalAmount, 0 , '.' , '.' )}}</p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <a href="/{{$id}}/topup/BiPay" class="topup_button">
                <i class="fa-solid fa-plus"aria-hidden="true" style="color: #ffffff;"></i>
                <p>TOP UP</p>
            </a>
        </div>

        <div class="history">
            <div class="history-container">
                @if ($transactions)
                    <div class="history-box-home"  onclick="window.location.href='/{{$id}}/order'" style="cursor: pointer;">
                        <div class="tenant-time">
                            <p style="font-weight: bold; font-size: 1.5vw;">{{$transactions->tenant_name}}</p>
                            <p style="font-size: 1.5vw;">{{$transactions->transaction_time}}</p>
                        </div>
                        <div class="hist">
                            <div class="food-order" style="display: flex;">
                                <p>{{$transactions->quantity}}x</p>
                                <p style="padding-left: 0.75vw">{{$transactions->menu_name}}</p>

                            </div>
                            <p class="status" style="color: #FD6727;">In-Progress</p>
                        </div>
                    </div>
                @elseif ($orders)
                    <div class="history-box-home"onclick="window.location.href='/{{$id}}/order'" style="cursor: pointer;">
                        <div class="tenant-time">
                            <p style="font-weight: bold; font-size: 1.5vw;">{{$orders->tenant_name}}</p>
                            <p style="font-size: 1.5vw;">{{$orders->orders_time}}</p>
                        </div>
                        <div class="hist">
                            <div class="food-order" style="display: flex;">
                                <p>{{$orders->quantity}}x</p>
                                <p style="padding-left: 0.75vw">{{$orders->menu_name}}</p>

                            </div>
                            @if ($orders->confirmStatus == "confirm")
                                <p class="status" style="background-color: #08A618; color: white; padding-right: 0.75vw">Completed</p>
                            @else
                                <p class="status" style="background-color: white; color: #FD6727; padding-right: 0.75vw; border: solid #FD6727">Confirm Pick Up?</p>
                            @endif

                        </div>
                    </div>

                @else
                    <div class="history-box-home" style="display: flex; justify-content: center; align-items: center;">
                        <p style="font-size: 2vw; text-align:center;">No Transaction For Today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="food">
        @if (request('search') or request('category'))
            @if ($tenant->count())
                @if (request('category'))
                    @foreach ($tenant_category as $tc)
                        @if ($tenant[0]->tenant_category_id == $tc['id'])
                            <div class="category-1">
                                <i onclick="window.location.href='/{{$id}}/homepage'" class='fas fa-angle-left'></i>
                                <p style="margin-left: 1vw" >{{$tc['name']}}</p>
                            </div>
                            @break
                        @endif
                    @endforeach
                @endif
            @else
                <p class="no_result">No Result Found</p>
            @endif

            <div class="tenant-1">
                @foreach ($tenant as $t)
                    <div style="margin-bottom: 1.5vw; cursor: pointer; "class="box" onclick="window.location.href='/{{$id}}/menu/{{$t->name}}'">
                        <div class="img">
                            <img src={{ asset('storage/tenant_images/'.$t->image_link) }} alt="">
                        </div>
                        <p style="font-size: 1.5vw;">{{$t['name']}}</p>
                    </div>
                @endforeach
            </div>
        @else
            @foreach ($tenant_category as $tc)
                <div class="Category">
                    <p>{{$tc['name']}}</p>
                    <a href="/{{$id}}/homepage/?category={{ $tc->id }}"><i class='fas fa-angle-right'></i></a>
                </div>
                <div class="tenant">

                    @php
                        $count = 0;
                    @endphp
                    @foreach ($tenant as $t)
                        @if ($t['tenant_category_id'] == $tc['id'])
                            @if ($count < 4)
                                <a class="box" href="/{{$id}}/menu/{{$t->name}}">
                                    <div class="img">
                                        <img src={{ asset('storage/tenant_images/'.$t->image_link) }} alt="">
                                    </div>
                                    <p style="font-size: 1.5vw;">{{$t['name']}}</p>
                                </a>

                                @php
                                    $count++;
                                @endphp
                            @else
                                @break
                            @endif

                        @endif
                    @endforeach
                </div>

            @endforeach
        @endif

    </div>
@endsection

@push('styles')
    <link href="{{asset('css/homepage.css')}}" rel="stylesheet" />
@endpush

