<header class="p-3 bg-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Wira Wiri Solo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('rental') ? 'active' : '' }}" href="/rental">Daftar Mobil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('wisata') ? 'active' : '' }}" href="/wisata">Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('ulasan') ? 'active' : '' }}" href="/ulasan">Ulasan</a>
                    </li>
                </ul>
                @auth
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Halo, {{ auth()->user()->nama_user }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fa fa-history" aria-hidden="true"></i> Riwayat Pesanan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @can('admin')
                            <li><a class="dropdown-item" href="/admin"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @endcan
                            <li>
                                <form action="/logout" method="POST" >
                                    @csrf
                                    <button type="submit" class="dropdown-item" href="#"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li> 
                </ul>
                @else
                    <div class="text-end ms-auto">
                        <button type="button" class="btn btn-outline-light me-2" onclick="window.location.href='/login'">Login</button>
                        <button type="button" class="btn btn-warning" onclick="window.location.href='/register'">Sign-up</button>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>