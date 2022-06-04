<style>
    #error {
        position: fixed;
        width: 60%;
        top: 50px;
        left: 50%;
        z-index: 1100;
        transform: translateX(-50%);
        font-size: 14px;
        font-weight: 300;
        /*animation: fadeInDown 1s forwards;*/
    }

    #error>div {
        padding: 12px 8px;
        margin: 10px;
        line-height: 1.5rem;
        transition: all 1s;
        text-align: right;
        border-radius: 10px;
    }

    #error>div.warning {
        background-color: #cbb000;
        color: #fff;
        border: 1px solid #b28706;
        -webkit-box-shadow: 0 0 7px 1px #ffeaa1;
        box-shadow: 0 0 7px 1px #ffeaa1;
    }

    #error>div.danger {
        background-color: #ed4a58;
        color: #fff;
        border: 1px solid #d53e4d;
        -webkit-box-shadow: 0 0 7px 1px #ffacb4;
        box-shadow: 0 0 7px 1px #ffacb4;
    }

    #error>div.success {
        background-color: #09c22e;
        color: #fff;
        border: 1px solid #2b9f6a;
        -webkit-box-shadow: 0 0 7px 1px #a3ffb5;
        box-shadow: 0 0 7px 1px #a3ffb5;
    }

    #error.close {
        /*animation: fadeOut 1s fade-out-delay forwards;*/
        animation: fadeOut 1s 3s forwards;
    }

    @keyframes fadeInDown {
        0% {
            opacity: 0;
            top: 0;
        }

        100% {
            opacity: 1;
            top: 50px;
        }
    }

    @keyframes fadeOut {
        0% {
            opacity: 1;
            pointer-events: auto;
        }

        100% {
            opacity: 0;
            pointer-events: none;
        }
    }

    @media (max-width: 768px) {
        #error>div>strong {
            display: block;
        }
    }

</style>

<div id="error">
    @foreach ($errors->all() as $error)
        <div class="danger">
            <strong>خطا! </strong>
            {{ $error }}
        </div>
    @endforeach
</div>

<div id="error">
    @if (Session::get('successful') != null)
        <div class="success">
            <strong>انجام شد! </strong> {{ Session::pull('successful') }}
        </div>
    @endif
</div>

<script src='{{ asset('js/error/error.js') }}'></script>
