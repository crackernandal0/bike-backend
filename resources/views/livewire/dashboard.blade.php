   @section('dashboard_active', 'active')
   <!-- Content wrapper -->
   <div class="content-wrapper">
       <!-- Content -->
       <div class="container-xxl flex-grow-1 container-p-y">
           <div class="row">
               <div class="col-12">
                   <div class="card">
                       <div class="d-flex align-items-end row">
                           <div class="col-sm-7">
                               <div class="card-body">
                                   <h5 class="card-title text-primary">Welcome, Admin! 🌟</h5>
                                   <p class="mb-4">
                                       Access all your powerful tools and features to manage your platform effectively.
                                   </p>
                               </div>
                           </div>
                           <div class="col-sm-5 text-center text-sm-left">
                               <div class="">
                                   <img class="svg" src="{{ asset('admin/assets/img/illustrations/admin.svg') }}"
                                       height="180" alt="User Profile">
                               </div>
                           </div>
                       </div>
                   </div>


               </div>
               <div class="col-12 mt-5 order-1">
                   <div class="row">
                       <div class="col-lg-6 col-md-12 col-6 mb-4">
                           <div class="card">
                               <div class="card-body">
                                   <div class="card-title d-flex align-items-start justify-content-between">
                                       <div class="avatar flex-shrink-0">
                                           <div class="">
                                               <img src="{{ asset('admin/assets/img/icons/unicons/users-alt.svg') }}"
                                                   alt="Credit Card" class="rounded" />
                                           </div>
                                       </div>
                                   </div>
                                   <span>Total Users</span>
                                   <h3 class="card-title text-nowrap mb-1">{{ $users ?? null }}</h3>
                               </div>
                           </div>
                       </div>



                       <div class="col-lg-6 col-md-12 col-6 mb-4">
                           <div class="card">
                               <div class="card-body">
                                   <div class="card-title d-flex align-items-start justify-content-between">
                                       <div class="avatar flex-shrink-0">
                                           <div>
                                               <img src="{{ asset('admin/assets/img/icons/unicons/ride.svg') }}"
                                                   alt="Credit Card" class="rounded" />
                                           </div>
                                       </div>
                                   </div>
                                   <span>Ride Bookings</span>
                                   <h3 class="card-title text-nowrap mb-1">{{ $rides ?? null }}</h3>
                               </div>
                           </div>
                       </div>

                       <div class="col-lg-6 col-md-12 col-6 mb-4">
                           <div class="card">
                               <div class="card-body">
                                   <div class="card-title d-flex align-items-start justify-content-between">
                                       <div class="avatar flex-shrink-0">
                                           <div>
                                               <img src="{{ asset('admin/assets/img/icons/unicons/hire.svg') }}"
                                                   alt="Credit Card" class="rounded" />
                                           </div>
                                       </div>
                                   </div>
                                   <span>Chauffeur Hires</span>
                                   <h3 class="card-title text-nowrap mb-1">{{ $chauffeurHires ?? null }}</h3>
                               </div>
                           </div>
                       </div>

                       <div class="col-lg-6 col-md-12 col-6 mb-4">
                           <div class="card">
                               <div class="card-body">
                                   <div class="card-title d-flex align-items-start justify-content-between">
                                       <div class="avatar flex-shrink-0">
                                           <div>
                                               <img src="{{ asset('admin/assets/img/icons/unicons/driving-school.svg') }}"
                                                   alt="Credit Card" class="rounded" />
                                           </div>
                                       </div>
                                   </div>
                                   <span>Driving School</span>
                                   <h3 class="card-title text-nowrap mb-1">{{ $drivingSchools ?? null }}</h3>
                               </div>
                           </div>
                       </div>


                       <div class="col-lg-6 col-md-12 col-6 mb-4">
                           <div class="card">
                               <div class="card-body">
                                   <div class="card-title d-flex align-items-start justify-content-between">
                                       <div class="avatar flex-shrink-0">
                                           <div>
                                               <img src="{{ asset('admin/assets/img/icons/unicons/comment-medical.svg') }}"
                                                   alt="Credit Card" class="rounded" />
                                           </div>
                                       </div>
                                   </div>
                                   <span>Support Requests</span>
                                   <h3 class="card-title text-nowrap mb-1">{{ $contactQueries ?? 0 }}</h3>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>

       </div>
       <!-- / Content -->


       <div class="content-backdrop fade"></div>
   </div>
   <!-- Content wrapper -->
