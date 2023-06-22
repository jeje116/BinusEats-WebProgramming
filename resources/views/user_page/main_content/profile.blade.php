@extends('/user_page.main_template')

@section('content')
    @if ($errors->has('fullname') or $errors->has('email') or $errors->has('phonenumber'))
        <script>
            window.location.href = window.location.href.split('#')[0] + '#popupedit';
        </script>
    @elseif ($errors->has('image'))
    <script>
        window.location.href = window.location.href.split('#')[0] + '#popup_edit_image';
    </script>
    @endif
    <div id="all_profile" class="allprofile">

        <div class="partTopUp">
            <p id="text" class="Ballance"><strong>Balance</strong></p>

            @foreach ($emoneys as $emoney)
            <div class="emoney_content">
                <div class="logoNtext">
                    <div class="logo">
                        <img id="logo" src="{{ $emoney->img }}" alt="Logo {{ $emoney->name }}">
                    </div>

                    <div class="paymentname">
                        <p class="payment">{{ $emoney->name }}</p>
                    </div>

                </div>

                <div class="total_amount">
                    <p> Rp{{number_format($moneys[$emoney->id-1]->totalAmount, 0 , '.' , '.' )}}</p>
                </div>
            </div>
            @endforeach

            <div class="Topupbutton">
                <button type="submit" id="button" value="topup" class="TopUpButton" onclick="window.location.href='/{{$id}}/topup/BiPay'">
                    <p id="Topup">
                        <strong>TOP UP</strong>
                    </p>
                </button>
            </div>

        </div>
        <div class="partPofile">
            <div class="bagianatas">
                <a href="#popup_edit_image">
                    <div class="profile-image">
                        <img class="foto" src={{ asset( $user->image_link) }} alt="Profile Image">
                        <div class="edit-overlay" onclick="showEditImageProfile()">
                            <span class="edit-text">Edit Photo</span>
                        </div>
                    </div>
                </a>
                <div class="welcome-edit">
                    <div class="welcome">
                        <p>Welcome!</p>
                        <strong style="text-transform: uppercase;">{{$user->name}}</strong>
                    </div>
                    <div class="editbutton">
                        <a href="#popupedit" class="editButton">
                            <div class="edittxt"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="datadiri">
                <strong>Full Name</strong>
                <p class="isidata"style="text-transform: capitalize">{{$user->name}}</p>

                <strong>Email</strong>
                <P class="isidata">{{$user->email}}</P>

                <strong>Phone Number</strong>
                @if ($user->phone)
                    <p class="isidata">{{$user->phone}}</p>
                @else
                    <p class="isidata">-</p>
                @endif

            </div>

            <div class="signoutbutton">
                <form action="/signout" method="post">
                    @csrf
                    <button type="submit" id="button" value="edit" class="signoutButton">
                        <p id="signout">
                            <strong>SIGN OUT</strong>
                        </p>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="popupeditimage" id="popup_edit_image">
        <form class="partPopUpEditProfile" action="/{{$id}}/profile/edit_profile_image" method="post" enctype="multipart/form-data">
            @csrf
            <a href="/{{$id}}/profile"><i class="fa fa-arrow-left back_sign"></i></a>
            <div class="editprofiletext">
                <strong>EDIT IMAGE</strong>
            </div>
            <div class="popup-profile-image">
                <img class="img-preview" src={{ asset( $user->image_link) }} alt="Profile Image">
            </div>
            <input type="hidden" value="{{ $user->image_link }}" name="prev_image">
            <input type="file" name="image" required id="image" onchange="previewImage()">
            @error('image')
                <div class="invalid">
                    {{ $message }}
                </div>
            @enderror

            <div class="save-wrapper">
                <div class="savebuttoneditimage">
                    <button class="savebuttoneditimage" type="submit">
                        <p id="save">
                            <strong>SAVE</strong>
                        </p>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="popupedit" id="popupedit">
        <div class="popupContent">
            <div class="editprofiletext">
                <a href="/{{$id}}/profile"><i class="fa fa-arrow-left back_sign"></i></a>
                <strong>EDIT PROFILE</strong>
            </div>

            <form action="{{ route('edit.profile', $user->id)}}" method="POST" class="editprofile" id="editProfileForm">
                @csrf
                <div class="name">
                    <strong>Full Name</strong><br>
                    <input type="text" name="fullname" class="inputtext" id="name" placeholder="{{$user->name}}" style="text-transform: capitalize">
                    @error('fullname')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="email">
                    <strong>Email</strong><br>
                    <input type="email" name="email" class="inputtext" id="email" placeholder="{{$user->email}}">
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="phonenumber">
                    <strong>Phone Number</strong><br>
                    @if ($user->phone)
                        <input type="number" name="phonenumber" class="inputtext input_phone_number" id="phonenumber" placeholder="{{$user->phone}}">
                    @else
                        <input type="number" name="phonenumber" class="inputtext input_phone_number" id="phonenumber" placeholder="-">
                    @endif
                    @error('phonenumber')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="Allbutton">
                    <div class="save-wrapper">
                        <div class="discardbutton" id="discardButtonContainer" style="display: none;">
                            <button id="discardButton" value="edit" class="discardButton" onclick="discardForm(event)">
                                <p id="discard">
                                    <strong>DISCARD</strong>
                                </p>
                            </button>
                        </div>

                        <div class="savebutton">
                            <button id="savebutton" value="edit" class="saveButton" onclick="changeInputValue(event)">
                                <p id="save">
                                    <strong>SAVE</strong>
                                </p>
                            </button>
                        </div>
                    </div>
                </div>

                <style>
                    input.placeholder-color {
                        color: #999999; /* Set the color of the placeholder text */
                    }
                </style>

                <script>
                    function discardForm(event) {
                        event.preventDefault(); // Prevent form submission
                        document.getElementById('editProfileForm').reset();
                        document.getElementById('discardButtonContainer').style.display = 'none';
                    }

                    const inputs = document.querySelectorAll('.inputtext');

                    inputs.forEach(input => {
                        input.addEventListener('input', () => {
                            const discardButtonContainer = document.getElementById('discardButtonContainer');
                            if (input.value.trim().length > 0) {
                                discardButtonContainer.style.display = 'block';
                            } else {
                                discardButtonContainer.style.display = 'none';
                            }
                        });
                    });
                </script>

                <script>
                    function changeInputValue(event) {
                        const inputName = document.getElementById('name');
                        const inputEmail = document.getElementById('email');
                        const inputPhone = document.getElementById('phonenumber');

                        if(!inputName.value){
                            inputName.value = inputName.placeholder;
                            inputName.classList.add('placeholder-color');
                        }
                        if(!inputEmail.value){
                            // inputName.removeAttribute('name');
                            inputEmail.value = inputEmail.placeholder;
                            inputEmail.classList.add('placeholder-color');
                        }
                        if(!inputPhone.value){
                            // inputName.removeAttribute('name');
                            inputPhone.value = $user->phone;
                            inputPhone.classList.add('placeholder-color');
                        }
                        var form = document.getElementById("editProfileForm");
                        form.submit();
                    }
                </script>
            </form>
        </div>
    </div>

    <script>
        function previewImage() {
            const image = document.querySelector( '#image' );

            const imgPreview = document.querySelector( '.img-preview' );

            const oFReader = new FileReader( );
            console.log(image, imgPreview, oFReader);
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }
    </script>
@endsection


@push('styles')
    <link href="{{asset('css/profile.css')}}" rel="stylesheet" />
@endpush
