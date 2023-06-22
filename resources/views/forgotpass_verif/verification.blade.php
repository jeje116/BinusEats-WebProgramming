@extends('/forgotpass')

@section('content')
    <div class="formsection">
        <form action="/verification" id="form" method="post">
            @csrf
            <input type="hidden" name="action_link" value="{{ $action_link }}">
            <input type="hidden" name="body" value="{{ $body }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="isiform">
                <p id="verificationtext">
                    Please check your email to reset your password. If the email doesn't show up, you can click the resend verification email button.
                </p>
                <div class="part_button">
                    <button type="submit" id="button" value="verification">
                        <p id="verification">
                            <strong>RESEND VERIFICATION EMAIL</strong>
                        </p>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
