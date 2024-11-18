<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            {{-- <span class="app-brand-logo demo"> --}}
                <img class="img-fluid" src="{{ asset('admin/assets/img/logo.png') }}" alt="logo">
                {{-- </span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard -->

        <li class="menu-item @yield('dashboard_active') mt-4">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-home-heart"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Management</span>
        </li>

        <li class="menu-item @yield('rides_active')">
            <a href="{{ route('rides') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-location-plus"></i>
                <div data-i18n="Ride Bookings">Rides</div>
            </a>
        </li>
        <li class="menu-item @yield('users_active') mt-2">
            <a href="{{ route('users') }}" class="menu-link">
                <svg class="menu-icon tf-icons" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <circle cx="9.00098" cy="6" r="4" fill="currentColor"></circle>
                        <ellipse cx="9.00098" cy="17.001" rx="7" ry="4" fill="currentColor">
                        </ellipse>
                        <path
                            d="M20.9996 17.0005C20.9996 18.6573 18.9641 20.0004 16.4788 20.0004C17.211 19.2001 17.7145 18.1955 17.7145 17.0018C17.7145 15.8068 17.2098 14.8013 16.4762 14.0005C18.9615 14.0005 20.9996 15.3436 20.9996 17.0005Z"
                            fill="currentColor"></path>
                        <path
                            d="M17.9996 6.00073C17.9996 7.65759 16.6565 9.00073 14.9996 9.00073C14.6383 9.00073 14.292 8.93687 13.9712 8.81981C14.4443 7.98772 14.7145 7.02522 14.7145 5.99962C14.7145 4.97477 14.4447 4.01294 13.9722 3.18127C14.2927 3.06446 14.6387 3.00073 14.9996 3.00073C16.6565 3.00073 17.9996 4.34388 17.9996 6.00073Z"
                            fill="currentColor"></path>
                    </g>
                </svg>
                <div data-i18n="Ride Bookings">Users</div>
            </a>
        </li>

        <li class="menu-item @yield('drivers_active') mt-3">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <svg class="menu-icon tf-icons" fill="#000000" viewBox="0 0 24 24" id="steering-wheel"
                    data-name="Flat Color" xmlns="http://www.w3.org/2000/svg" class="icon flat-color">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path id="primary"
                            d="M12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Zm0,2a8,8,0,0,1,7.38,4.92A29.93,29.93,0,0,0,12,8a29.63,29.63,0,0,0-7.4.94A8,8,0,0,1,12,4ZM4,12.67l1.11-.13A4.38,4.38,0,0,1,10,16.89v2.85A8,8,0,0,1,4,12.67Zm10,7.07V16.89a4.38,4.38,0,0,1,4.86-4.35l1.11.13A8,8,0,0,1,14,19.74Z"
                            style="fill: currentColor;"></path>
                    </g>
                </svg>
                <div>Drivers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @yield('approved_drivers_active') mt-3">
                    <a href="{{ route('approved-drivers') }}" class="menu-link">
                        <div class="small">Approved Drivers</div>
                    </a>
                </li>
                <li class="menu-item @yield('pending_drivers_active') mt-3">
                    <a href="{{ route('pending-drivers') }}" class="menu-link">
                        <div class="small">Driver Requests</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item @yield('chauffeurs_active') mt-3">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <svg class="menu-icon tf-icons" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path
                            d="M21.091 6.97953C20.241 6.03953 18.821 5.56953 16.761 5.56953H16.521V5.52953C16.521 3.84953 16.521 1.76953 12.761 1.76953H11.241C7.48101 1.76953 7.48101 3.85953 7.48101 5.52953V5.57953H7.24101C5.17101 5.57953 3.76101 6.04953 2.91101 6.98953C1.92101 8.08953 1.95101 9.56953 2.05101 10.5795L2.06101 10.6495L2.13847 11.4628C2.15273 11.6126 2.2334 11.7479 2.35929 11.8303C2.59909 11.9872 3.00044 12.2459 3.24101 12.3795C3.38101 12.4695 3.53101 12.5495 3.68101 12.6295C5.39101 13.5695 7.27101 14.1995 9.18101 14.5095C9.27101 15.4495 9.68101 16.5495 11.871 16.5495C14.061 16.5495 14.491 15.4595 14.561 14.4895C16.601 14.1595 18.571 13.4495 20.351 12.4095C20.411 12.3795 20.451 12.3495 20.501 12.3195C20.8977 12.0953 21.3093 11.819 21.6845 11.5484C21.7975 11.4668 21.8698 11.3408 21.8852 11.2023L21.901 11.0595L21.951 10.5895C21.961 10.5295 21.961 10.4795 21.971 10.4095C22.051 9.39953 22.031 8.01953 21.091 6.97953ZM13.091 13.8295C13.091 14.8895 13.091 15.0495 11.861 15.0495C10.631 15.0495 10.631 14.8595 10.631 13.8395V12.5795H13.091V13.8295ZM8.91101 5.56953V5.52953C8.91101 3.82953 8.91101 3.19953 11.241 3.19953H12.761C15.091 3.19953 15.091 3.83953 15.091 5.52953V5.57953H8.91101V5.56953Z"
                            fill="currentColor"></path>
                        <path
                            d="M20.8733 13.7349C21.2269 13.5666 21.6342 13.8469 21.5988 14.2369L21.2398 18.1907C21.0298 20.1907 20.2098 22.2307 15.8098 22.2307H8.18984C3.78984 22.2307 2.96984 20.1907 2.75984 18.2007L2.41913 14.4529C2.38409 14.0674 2.78205 13.7874 3.13468 13.947C4.2741 14.4625 6.37724 15.3771 7.67641 15.7174C7.84072 15.7604 7.97361 15.878 8.04556 16.0319C8.65253 17.33 9.96896 18.0207 11.8698 18.0207C13.752 18.0207 15.085 17.3034 15.694 16.0021C15.766 15.8481 15.8991 15.7305 16.0635 15.6873C17.443 15.3243 19.6816 14.3019 20.8733 13.7349Z"
                            fill="currentColor"></path>
                    </g>
                </svg>
                <div>Chauffeur Hire</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @yield('chauffeurs_profile_active') mt-3">
                    <a href="{{ route('chauffeurs') }}" class="menu-link">
                        <div class="small">Chauffeurs</div>
                    </a>
                </li>
                <li class="menu-item @yield('chauffeur_bookings_active') mt-3">
                    <a href="{{ route('chauffeur-bookings') }}" class="menu-link">
                        <div class="small">Chauffeur Bookings</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item @yield('vehicles_active') mt-3">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bxs-car"></i>
                <div>Vehicles</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @yield('vehicle_types_active') mt-3">
                    <a href="{{ route('vehicle-types') }}" class="menu-link">
                        <div class="small">Vehicle Types</div>
                    </a>
                </li>
                <li class="menu-item @yield('vehicle_subcategories_active') mt-3">
                    <a href="{{ route('vehicle-subcategories') }}" class="menu-link">
                        <div class="small">Vehicle Subcategories</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item @yield('zones_active') mt-3">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bxs-map-alt"></i>
                <div>Zones</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @yield('service_locations_active') mt-3">
                    <a href="{{ route('service-locations') }}" class="menu-link">
                        <div class="small">Service Locations</div>
                    </a>
                </li>
                <li class="menu-item @yield('all_zones_active') mt-3">
                    <a href="{{ route('zones') }}" class="menu-link">
                        <div class="small">Zones</div>
                    </a>
                </li>
                <li class="menu-item @yield('zone_vehicles_active') mt-3">
                    <a href="{{ route('zone-vehicles') }}" class="menu-link">
                        <div class="small">Zone Vehicles</div>
                    </a>
                </li>
            </ul>
        </li>



        <li class="menu-item @yield('push_notifications_active') mt-3">
            <a href="{{ route('push-notification') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-bell"></i>
                <div data-i18n="Dashboard">Push Notification</div>
            </a>
        </li>



    </ul>
</aside>