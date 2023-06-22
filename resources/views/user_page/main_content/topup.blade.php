@extends('/user_page.main_template')

@section('content')
@dump($snap_token, $order_id)
    <!-- Add a hidden modal box -->
    <div class="body-section">
        <div class="topup-container">
            <div class="emoneyButton">
                <a class="button" href="/{{ $user->id }}/topup/BiPay" style="background-color: #{{ $active == 1 ? 'F9C140': 'Fad685'}}; border-bottom:{{$active == 1?'none':"solid 0.2vw rgba(0, 0, 0, 0.2)"}}"><img src="{{$tr_emone[0]->img}}" alt=""> BiPay</a>
                <a class="button" href="/{{ $user->id }}/topup/OVO" style="background-color: #{{ $active == 2 ? 'F9C140': 'Fad685'}}; border-bottom:{{$active == 2?'none':"solid 0.2vw rgba(0, 0, 0, 0.2)"}}"><img src="{{$tr_emone[1]->img}}" alt="">OVO</a>
                <a class="button" href="/{{ $user->id }}/topup/GoPay"style="background-color: #{{ $active == 3 ? 'F9C140': 'Fad685'}};border-bottom:{{$active == 3?'none':"solid 0.2vw rgba(0, 0, 0, 0.2)"}}"><img src="{{$tr_emone[2]->img}}" alt="">GoPay</a>
            </div>
            
            <form action="/{{ $user->id }}/topup/process" method="POST" class="topup2" onsubmit="return sendDataMidtrans(event)">
                @csrf
                <input type="text" name="user_id" value="{{ $user->id }}" style="display: none;">
                <input type="text" name="emoney_id" value="{{ $emoney[0]->id }}" style="display: none;">
                <div class="saldo">
                    <div class="emone">
                        @foreach ($emoney as $em)
                            <img src="{{$em['img']}}" alt="">
                            <p style="margin-left:0.5vw">{{$em['name']}}</p>
                        @endforeach
                    </div>
                    @foreach ($money as $m)
                        @if ($m['user_id']==$id)
                            <p>Rp{{$m['formattedPrice']}}</p>
                        @endif

                    @endforeach
                </div>
                {{-- value="{{ $inputted_amount ? $inputted_amount : null }}" --}}
                <input id="inputAmount" class="amount" type="number" name="amount"  placeholder="INSERT AMOUNT.." required>

                <div class="button">
                    <button type="submit" class="topup_but" id="pay-button-temp">
                        <p>TOP UP</p>
                    </button>
                </div>
                
            </form>
            <button class="topup_but" id="pay-button" style="display: none">

            </button>

            <form action="/{{ $user->id }}/topup/finish" method="POST" style="display: none;" id="finish_top_up">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order_id }}">
                
            </form>

            <script type="text/javascript">
                // For example trigger on button clicked, or any time you need
                var payButton = document.getElementById('pay-button');
                payButton.addEventListener('click', function () {
                // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
                    window.snap.pay('{{ $snap_token }}', {
                        onSuccess: function(result){
                            alert("payment success!"); console.log(result);
                            document.getElementById('finish_top_up').submit();
                        },
                        onPending: function(result){

                            // var url = '/' + {{$id}} + '/topup/midtrans/pending?order=' + {{ $order_id }};

                            // console.log(url)
                            // return;
                            // fetch(url)
                            // .then(response => {
                            // if (response.ok) {
                            //     return response.json();
                            // } else {
                            //     throw new Error('Error: ' + response.status);
                            // }
                            // })
                            // .then(data => {
                            //     console.log('Response:', data);
                            //     })
                            //     .catch(error => {
                            //     console.error('Error:', error);
                            // });

                            alert("wating your payment!"); console.log(result);
                            document.getElementById('finish_top_up').submit();
                        },
                        onError: function(result){
                            alert("payment failed!"); console.log(result);
                            document.getElementById('finish_top_up').submit();
                        },
                        onClose: function(){
                            alert('you closed the popup without finishing the payment');
                            document.getElementById('finish_top_up').submit();
                        }
                    });
                });
              </script>

        </div>
        <div id="topupConfirmation" class="modal">
            <div class="modal-content">
                <h3 style="font-size: 3vw">Top Up Confirmation</h3>
                <p style="font-size: 1.5vw">Are you sure you want to top up your {{$emoney[0]->name}}?</p>

                <div class="modal-buttons">
                    <button style="font-size: 1.5vw" id="confirmTopUpButton" class="confirm-button">Yes</button>
                    <button style="font-size: 1.5vw" id="cancelTopUpButton" class="cancel-button">No</button>
                </div>
            </div>
        </div>

        <div class="history-help">
            <div class="history-topup">
                <div class="history-box">
                    <p class="judul">History</p>
                    <div class="topup-hist">
                        @if ($transaction->count())
                            @foreach ($transaction as $t)
                                <div class="date">
                                    <p>{{$t['transaction_day']}}, {{$t['transaction_date']}}</p>
                                </div>

                                <div class="topupp">
                                    <p>{{$t['method']}}</p>

                                    @if ($t['method']==="Payment")
                                        <p>- Rp{{number_format($t['amount'], 0 , '.' , '.' )}}</p>
                                    @else
                                        <p>+ Rp{{number_format($t['amount'], 0 , '.' , '.' )}}</p>
                                    @endif

                                </div>

                                <div class="time-hist">
                                    <p>{{$t['transaction_time']}}</p>
                                    @foreach ($tr_emone as $em)
                                        @if ($em['id']==$t['emoney_id'])
                                            <div class="emoney-hist">
                                                <img src="{{$em['img']}}" alt="">
                                                <p>{{$em['name']}}</p>
                                            </div>
                                            @break
                                        @endif
                                    @endforeach
                                </div>

                            @endforeach
                        @else
                            <p class="no_result">You Had Not Done Any Top Up</p>
                        @endif

                    </div>
                    

                </div>

            </div>
        </div>
        
        <script>
            window.addEventListener('DOMContentLoaded', getMidtransData('{{ $snap_token ? $snap_token: "null"}}', {{ $active-1 }}));
        </script>

    </div>
@endsection


@push('styles')
    <link href="{{asset('css/topup.css')}}" rel="stylesheet" />
@endpush

<script>

    var snap_token;
    var emoney_id;

    function getMidtransData(snap_token_data, emoney_id_data) {
        console.log(snap_token_data !== 'null', snap_token_data, emoney_id_data);
        if (snap_token_data !== 'null') {
            snap_token = snap_token_data;
            document.getElementById('pay-button').click();
        }

        emoney_id = emoney_id_data;
    }

    function sendDataMidtrans() {
        // event.preventDefault();

        var amount;

        if (snap_token == undefined) {

            amount = document.getElementById('inputAmount').value;

        }

        location.reload();
    }
</script>
