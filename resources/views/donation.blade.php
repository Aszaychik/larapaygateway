<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AsZDonation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <style>
        body {
            min-height: 75rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">AsZDonation</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Donation <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron">
        <div class="container">
            <h1 class="display-4">AsZDonation</h1>
            <p class="lead">If there is a will, there is a platform Donation.</p>
        </div>
    </div>

    <div class="container">
        <form action="#" id="donation_form">
            <legend>Donation</legend>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" name="name" class="form-control" id="name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">E-Mail</label>
                        <input type="email" name="email" class="form-control" id="email">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Donation Type</label>
                        <select name="donation_type" class="form-control" id="donation_type">
                          <option value="medical_treatment">Medical Treatment</option>
                          <option value="Humanity">Humanity</option>
                          <option value="natural_disaster">Natural Disaster</option>
                          <option value="religion">Religion</option>
                          <option value="scholarship">Scolarship</option>
                          <option value="infrastructure">Infrastructure</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Total</label>
                        <input type="number" name="amount" class="form-control" id="amount">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Note (Opsional)</label>
                        <textarea name="note" cols="30" rows="3" class="form-control" id="note"></textarea>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Kirim</button>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.4.1.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js">
    </script>
    <script
        type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.clientKey')
    }}">
    </script>
    <script>
        $("#donation_form").submit(function(event) {
            event.preventDefault();
            $.post("/api/", {
                _method: 'POST',
                _token: '{{ csrf_token() }}',
                name: $('input#name').val(),
                email: $('input#email').val(),
                donation_type: $('select#donation_type').val(),
                amount: $('input#amount').val(),
                note: $('textarea#note').val(),
            },
            function (data, status) {
                console.log(data);
                snap.pay(data.snap_token, {
                    // Optional
                    onSuccess: function (result) {
                        location.reload();
                    },
                    // Optional
                    onPending: function (result) {
                        location.reload();
                    },
                    // Optional
                    onError: function (result) {
                        location.reload();
                    }
                });
                return false;
            });
        })
    </script>
</body>

</html>
