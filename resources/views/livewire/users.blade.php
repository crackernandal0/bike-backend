@section('users_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Users</h4>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <h5 class="card-header">
                    <div class="row">

                        <div class="col-lg-6 d-flex align-items-center justify-content-between">
                            <span>Users</span>
                        </div>

                        <div class="col-lg-3">

                        </div>
                        <div class="col-lg-3 mt-4 mt-lg-0">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31"><i
                                        class="bx bx-search"></i></span>
                                <input type="text" wire:model.live.debounce.500ms="search" class="form-control"
                                    placeholder="Search..." aria-label="Search..."
                                    aria-describedby="basic-addon-search31">
                            </div>
                        </div>
                    </div>

                </h5>

                <div class="table-responsive text-wrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="font-size: 11px;white-space:nowrap">SNO.</th>
                                <th style="font-size: 11px;white-space:nowrap">Name</th>
                                <th style="font-size: 11px;white-space:nowrap">Email</th>
                                <th style="font-size: 11px;white-space:nowrap">Phone Number</th>
                                <th style="font-size: 11px;white-space:nowrap">Completed Rides</th>
                                <th style="font-size: 11px;white-space:nowrap">Active</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($users) > 0)
                                @foreach ($users as $index => $user)
                                    <tr class="cursor-pointer">
                                        <td>
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                        <td style="white-space:nowrap">{{ $user->name }}</td>

                                        <td style="white-space:nowrap"><a style="word-break: break-all;"
                                                class="text-decoration-underline"
                                                href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>

                                        <td style="white-space:nowrap"><a style="word-break: break-all;"
                                                class="text-decoration-underline"
                                                href="tel:{{ $user->country_code }}{{ $user->phone_number }}">{{ $user->country_code }}{{ $user->phone_number }}</a>
                                        </td>

                                        <td>
                                            @if ($user->active == 1)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this user?"
                                                    wire:click="changeStatus({{ $user->id }},0)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn">
                                                    <img width="20" height="20"
                                                        src="{{ asset('admin/assets/img/icons/unicons/verified.svg') }}"
                                                        alt="cross icon">
                                                </button>
                                            @elseif($user->active == 0)
                                                <button
                                                    wire:confirm="Are you sure you want to change the status of this user?"
                                                    wire:click="changeStatus({{ $user->id }},1)"
                                                    data-bs-toggle="tooltip" data-bs-offset="0,4"
                                                    data-bs-placement="top" data-bs-html="true" title=""
                                                    data-bs-original-title="<i class='bx bx-edit-alt bx-xs' ></i> <span>Tap To Change Status</span>"
                                                    class="btn">
                                                    <img width="15" height="15"
                                                        src="{{ asset('admin/assets/img/icons/unicons/cross.svg') }}"
                                                        alt="cross icon">
                                                </button>
                                            @endif
                                        </td>

                                        <td style="white-space:nowrap">{{ $user->completed_rides_count }}</td>


                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="15">
                                        No Record Found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center justify-content-end">
                            {{ $users->links('custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- / Content -->


        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->




    @include('livewire.notification')
</div>
