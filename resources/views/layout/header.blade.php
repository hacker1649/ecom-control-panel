<!-- Navbar -->
<header class="p-3 text-bg-dark bg-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                <span class="icon-cart"
                    style="font-size: 40px; display: inline-block; width: 40px; height: 40px; border-radius: 50%; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-store" style="font-size: 24px; color: #333;"></i>
                </span>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="#" class="nav-link px-2 text-secondary">Home</a></li>
                <li><a href="#" class="nav-link px-2 text-secondary">About</a></li>
                <li><a href="#" class="nav-link px-2 text-secondary">Products</a></li>
                <li><a href="#" class="nav-link px-2 text-secondary">Pricing</a></li>
                <li><a href="#" class="nav-link px-2 text-secondary">FAQs</a></li>
            </ul>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
                <div class="input-group" style="border: 3px solid #ddd; border-radius: 10px">
                    <span class="input-group-text bg-white border-0">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="search" class="form-control form-control-dark text-bg-white" placeholder="Search..."
                        aria-label="Search" style="width: 300px; border: none;">
                </div>
            </form>

            <div class="text-end">
                @guest
                    <div class="text-end">
                        <a href="{{ route('login') }}" class="btn btn-dark me-2">Login</a>
                    </div>
                @else
                    <li class="nav-item navbar-dropdown dropdown-user dropdown d-flex justify-content-center align-items-center mx-2">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <svg class="w-px-40 h-auto rounded-circle" xmlns="http://www.w3.org/2000/svg" width="40"
                                    height="40" viewBox="0 0 40 40">
                                    <circle cx="20" cy="20" r="18" fill="#f3f3f3" stroke="#ccc"
                                        stroke-width="2" />
                                    <foreignObject x="8" y="8" width="24" height="24">
                                        <i class="fa fa-user" style="font-size: 24px; color: #333;"></i>
                                    </foreignObject>
                                </svg>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <svg class="w-px-40 h-auto rounded-circle"
                                                    xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 40 40">
                                                    <circle cx="20" cy="20" r="18" fill="#f3f3f3"
                                                        stroke="#ccc" stroke-width="2" />
                                                    <foreignObject x="8" y="8" width="24" height="24">
                                                        <i class="fa fa-user" style="font-size: 24px; color: #333;"></i>
                                                    </foreignObject>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                            <small class="text-muted">{{ Auth::user()->email }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider my-1"></div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </div>

            <a href="{{ route('cart.index') }}" class="d-flex align-items-center ms-3 mb-2 mb-lg-0 text-dark text-decoration-none">
                <i class="fas fa-shopping-cart ms-1" style="font-size: 25px;"></i>
            </a>
        </div>
    </div>
</header>

<!-- / Navbar -->
