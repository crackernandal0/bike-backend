<div>
    <form wire:submit.prevent="loginUser" id="formAuthentication" class="mb-3" method="POST">
        
        
        @csrf
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bx bx-user"></i>
                </span>
                <input type="text" class="form-control @error('username')
                    is-invalid
                 @enderror" id="username" wire:model="username" placeholder="Enter your username" autofocus />
            </div>

            @error('username')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bx bx-key"></i>
                </span>
                <input type="password" id="password" class="form-control @error('password')
                    is-invalid
                 @enderror" wire:model="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" wire:model="remember" type="checkbox" id="remember-me" />
                <label class="form-check-label" for="remember-me"> Remember Me </label>
            </div>
        </div>
        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
        </div>
    </form>

    <livewire:notification />
</div>