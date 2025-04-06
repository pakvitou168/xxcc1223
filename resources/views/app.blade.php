@extends('layouts.app')
@section('content')

<body class="main">
    <div id="app">
        <side-menu></side-menu>
    </div>
    @auth
        <input type="hidden" id="authorized-functions"
            value="{{App\Http\Controllers\UserManagement\User\UserServiceController::getAuthorizedFunctions()}}">
    @endauth
</body>
@endsection