@extends('/user_page.main_template')

@section('content')
    @php
        $jenisArr = [];
    @endphp
    <div class="kontener">
        <div class="left">
            <div class="div-name-restoran">
                <p class="text-name-restoran"><i><u>{{ $tenant->name }}</u></i></p>
            </div>
            <div class="logo-binuseat">
                <img src="{{asset('/storage/tenant_images/')}}">
            </div>
        </div>
        <div class="right">
            <div class="title">
                <p class="text-title">{{$menu->name}}</p>
            </div>
            <div class="detail">
                <div class="price">
                    <p class="text-price">Rp{{number_format($menu->price, 0 , '.' , '.' )}}</p>
                </div>
                <div class="detail2">
                    <div class="stok">
                        <p class="text-stok">{{$menu->stock}} in stock</p>
                    </div>
                    <br>
                    <div class="batch">
                        <p class="text-batch" id="batch-text"><div class="jem"><p class="text-jem" id="jem-text"></p></div></p>
                    </div>
                    <script>
                        var currentTime = new Date();
                        var hours = currentTime.getHours();
                        var minutes = currentTime.getMinutes();

                        var batchNumber;
                        if (hours < 8 || (hours == 8 && minutes < 50)) {
                            batchNumber = 1;
                            batchEndTime = '08:50';
                        } else if (hours < 10 || (hours == 10 && minutes < 50)) {
                            batchNumber = 2;
                            batchEndTime = '10:50';
                        } else if (hours < 12 || (hours == 12 && minutes < 50)) {
                            batchNumber = 3;
                            batchEndTime = '12:50';
                        } else if (hours < 14 || (hours == 14 && minutes < 50)) {
                            batchNumber = 4;
                            batchEndTime = '14:50';
                        } else {
                            batchNumber = "No more batches for today";
                        }

                        var batchTextElement = document.getElementById("batch-text");
                        var jemTextElement = document.getElementById("jem-text");
                        if (batchNumber === "No more batches for today") {
                            batchTextElement.textContent = batchNumber;
                            jemTextElement.textContent = hours + ":";
                            if (minutes < 10) {
                                jemTextElement.textContent += "0";
                            }
                            jemTextElement.textContent += minutes;
                        } else {
                            batchTextElement.textContent = "Batch " + batchNumber + " ends at ";
                            jemTextElement.textContent = batchEndTime;
                        }
                    </script>
                </div>
            </div>
            <div class="jumlah">
                @if ($menu->jenis)
                    @php
                        $jenisArr = explode(",", $menu->jenis);
                    @endphp

                    @if ($jenisArr)
                        <script>
                            jenisArr = [];
                        </script>

                    @endif

                    <div class="pilih">
                        @foreach ($jenisArr as $item)
                            <div class="jenis">
                                <div class="kotak">
                                    <input id="{{$item}}" type="checkbox" id="checkbox" name="checkbox" required value="{{ $item }}" onclick="inputCheckHandle(value)">
                                </div>
                                <p>{{ $item }}</p>
                                <script>
                                    jenisArr.push("{{ $item }}");
                                </script>
                            </div>
                        @endforeach

                    </div>
                    @endif
                    <div id="errorInputCeck" class="errorInputCeck">Please choose between these {{count($jenisArr)}} items</div>
            </div>
            <div class="request">
                <div>
                    <input type="text" class="requesttxt" placeholder=" Additional requests..." onmouseleave="mouseLeaveHandle(value)">
                </div>
            </div>
            <div class="item_quantity_section_menu_detail">
                <div class="item_quantity_value_section_menu_detail">
                    <div id="hover_toggle" class="item_quantity_minus_sign_menu_detail" onclick="myFunction_minus({{ $menu->price }})" style="visibility: hidden;">
                        <i id="min_sign" class="fa fa-minus" aria-hidden="true"></i>
                    </div>
                    <div id="quality_value" class="item_quantity_value_menu_detail">1</div>
                    <div id="plus_sign" class="item_quantity_plus_sign_menu_detail" onclick="myFunction_plus({{ $menu->price }})"><i class="fa fa-plus" aria-hidden="true"></i></div>
                </div>
            </div>
            <div class="add-cart">
                <div class="add" onclick="addToCart('none')">
                    <p class="text-add">ADD TO CART</p>
                </div>
            </div>
        </div>

        <div class="popup-confirm" id="popup-confirm">
            <div class="isi">
                <div class="text-sukses">
                    <p class="teks-sukses"><strong>Order successfully added to cart!</strong></p>
                </div>
                <div class="text-ok" onclick="location.reload()">
                    <p class="teks-ok"><strong>OK</strong></p>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', sendData({{ $menu->price}}, {{ $menu->stock }}, {{ $menu->id }}));
        </script>

        @if ($menu->stock == 1)
            <script>
                stop_hover("plus_sign");
            </script>

        @endif
@endsection

@push('styles')
    <link href="{{asset('css/menudetail.css')}}" rel="stylesheet" />
@endpush


<script>
    var element;
    var quality_total = 1;
    var totalStock = 0;
    var jenisArr = null;
    var menuId = 0;
    var price = 0;
    var additionalDescription = null;
    var jenis = null;

    function updateViewTotal() {
        document.getElementById('orderSubtotal').innerHTML = quality_total * price;
    }

    function stop_hover(id) {
        document.getElementById(id).style.visibility = "hidden";
    }

    function start_hover(id) {
        document.getElementById(id).style.visibility = "visible";
    }

    function myFunction_plus(price) {
        element = document.getElementById("quality_value");
        quality_total = element.innerHTML;
        quality_total++;

        if (quality_total==totalStock) {
            stop_hover("plus_sign");
        }

        if(quality_total>=1) {
            start_hover("hover_toggle");
        }

        element.innerHTML = quality_total;
        updateViewTotal();
    }

    function myFunction_minus(price) {
        element = document.getElementById("quality_value");
        quality_total = element.innerHTML;
        quality_total--;
        if (quality_total==1) {
            stop_hover("hover_toggle");
        }
        if (quality_total<=totalStock) {
            start_hover("plus_sign");
        }

        element.innerHTML = quality_total;
        updateViewTotal();
    }

    function inputCheckHandle(jenisData) {
        element = document.getElementById(jenisData).checked

        if (element) {
            jenisArr.forEach(i => {
                if (i == jenisData) {
                    jenis = jenisData;
                }
                else {
                    document.getElementById(i).checked = false;
                }
            });
        }

        else {
            jenis = null;
        }


        hideErrorInput('errorInputCeck');
    }

    function mouseLeaveHandle(value) {
        additionalDescription = value;
    }

    function sendData(priceData, stockData, menuIdData) {
        price = priceData;
        totalStock = stockData;
        menuId = menuIdData;
    }

    function showErrorInput(idElement) {
        document.getElementById(idElement).style.display = 'block';
    }

    function hideErrorInput(idElement) {
        document.getElementById(idElement).style.display = 'none';
    }

    function addToCart() {

        if (jenisArr != null) {
            if (jenis == null) {
                showErrorInput('errorInputCeck');
                return;
            }
            console.log(jenisArr);
        }

        var url = '/' + {{$id}} + '/menu_detail/add_to_cart?menuId=' + menuId + '&quantity=' + quality_total + '&additionalDescription=' + additionalDescription + '&jenis=' + jenis;

        fetch(url)
        .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Error: ' + response.status);
        }
        })
        .then(data => {
            console.log('Response:', data);
            })
            .catch(error => {
            console.error('Error:', error);
        });
        popUpHandle('flex');
    }

    function popUpHandle(displayData) {
        document.getElementById('popup-confirm').style.display = displayData;
        // location.reload();
    }
</script>



