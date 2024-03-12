<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MichuJobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

    <nav class="navbar navbar-expand-lg  bg-dark" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="/">MichuJobs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="">Home</a>
                    </li>
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img src="{{ Storage::url(auth()->user()->profile_pic ?? '') }}" width="40"
                                    class="rounded-circle">
                            </button>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                        href="{{ route('seeker.profile') }}">Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                        href="{{ route('job.applied') }}">Applied jobs</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="logout" href="#">Logout</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if (!Auth::check())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('create.seeker') }}">Job Seeker</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('create.employer') }}">Employer</a>
                        </li>
                    @endif

                    <form id="form-logout" action="{{ route('logout') }}" method="post">@csrf</form>
                </ul>
            </div>
        </div>
    </nav>
    @yield('content')
    <script>
        let logout = document.getElementById('logout');
        let form = document.getElementById('form-logout');
        logout.addEventListener('click', function() {
            form.submit();
        })
    </script>
</body>

</html>
