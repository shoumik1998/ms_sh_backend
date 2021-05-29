@extends('app')



@section('content')

    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4 p-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 id="score">.....</h2>
                        <h5>Live Score</h5>

                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')

    <script type="text/javascript">

        var pusher=new Pusher('ffef667936b10eda0612',{
            cluster:'ap2'
        });

        var channel=pusher.subscribe('mychannel');
        channel.bind('myevent', function (data) {
            $('#score').html(JSON.stringify(data))

        });


    </script>


@endsection
