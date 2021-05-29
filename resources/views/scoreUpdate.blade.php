@extends('app')

@section('content')

    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-4 p-3">
                <div class="card text-center">
                    <div class="card-body">
                        <input class="form-control ScoreValue" type="text"><br>
                        <button class=" btn updatebtn btn-block btn-success">Update</button>
                        <h4 class="Lastscore"></h4>
                    </div>

                </div>

            </div>
        </div>

    </div>


@endsection


@section('script')
    <script type="text/javascript">
        $('.updatebtn').click(alert('kjh'));
        // $('.updatebtn').click(function () {
        //     alert('hmmm');
        //     var score=$('.ScoreValue');
        //     var url="/pusher"
        //     axios.post(url,{msg:score}).then(function (response) {
        //         $('.Lastscore').html(response.data)
        //
        //
        //     }).catch(function (exception) {
        //         //$('.Lastscore').html(exception.data)
        //
        //     });
        // });
    </script>




@endsection



