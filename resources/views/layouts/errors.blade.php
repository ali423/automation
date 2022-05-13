<style>
    #error {
        position: fixed;
        width: 60%;
        top: 50px;
        left: 50%;
        z-index: 1100;
        padding: 12px 8px;
        transition:all 1s;
        text-align: right;
        border-radius: 10px;
        transform: translateX(-50%);
        /*animation: fadeInDown 1s forwards;*/
    }
    #error.warning{
        background-color: #ffeaa1;
        color:#664d03;
        border: 1px solid #b28706;
        -webkit-box-shadow: 0 0 7px 1px #ffeaa1;
        box-shadow: 0 0 7px 1px #ffeaa1;
    }
    #error.danger{
        background-color: #ffacb4;
        color:#842029;
        border: 1px solid #d53e4d;
        -webkit-box-shadow: 0 0 7px 1px #ffacb4;
        box-shadow: 0 0 7px 1px #ffacb4;
    }
    #error.success{
        background-color: #a3ffb5;
        color:#0f5132;
        border: 1px solid #2b9f6a;
        -webkit-box-shadow: 0 0 7px 1px #a3ffb5;
        box-shadow: 0 0 7px 1px #a3ffb5;
    }

    #error.close{
        /*animation: fadeOut 1s fade-out-delay forwards;*/
        animation: fadeOut 1s 2s forwards;
    }

    @keyframes fadeInDown{
        0%{
            opacity:0;
            top:0;
        }
        100%{
            opacity: 1;
            top:50px;
        }
    }
    @keyframes fadeOut{
        0%{
            opacity:1;
            pointer-events: auto;
        }
        100%{
            opacity: 0;
            pointer-events: none;
        }
    }

    @media (max-width: 768px){
        #error>strong{
            display: block;
        }
    }
</style>

@foreach($errors->all() as $error)
    <div id="error" class="danger">
        <strong>خطا! </strong>{{$error}}
    </div>
@endforeach

@if(Session::get('successful')!=null)
    <div id="error" class="success">
        <strong>انجام شد! </strong> {{Session::pull('successful')}}
    </div>
@endif

<script src='{{asset('js/error/error.js')}}'></script>
