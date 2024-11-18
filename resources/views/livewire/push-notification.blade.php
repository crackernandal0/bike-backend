@section('push_notifications_active', 'active')
<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Push Notification</h4>

            <!-- Basic Bootstrap Table -->
            <div class="row">
                <div class="card col-lg-8 mx-auto">
                    <div class="card-body">

                        <div>
                            <label class="font-semibold mb-1">Title</label>
                            <input type="text" wire:model="title" id="title" class="form-control">
                            @error('title')
                                <div class="my-2 text-red-500 text-sm font-semibold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="font-semibold mb-1">Description</label>
                            <textarea wire:model="description" id="description" class="form-control" rows="5"></textarea>
                            @error('description')
                                <div class="my-2 text-red-500 text-sm font-semibold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="font-semibold mb-1">Send To</label>
                            <select wire:model="type" id="type" class="form-control">
                                <option value="users">Users</option>
                                <option value="drivers">Drivers</option>
                                <option value="instructors">Instructors</option>
                                <option value="all">All</option>
                            </select>
                            @error('type')
                                <div class="my-2 text-red-500 text-sm font-semibold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <button wire:click.prevent="send" class="btn btn-primary mt-5" wire:loading.attr="disabled">
                            <span wire:loading.remove>Send</span>
                            <div wire:loading>
                                Loading...
                            </div>

                        </button>

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
