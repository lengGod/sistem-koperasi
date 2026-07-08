<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-bold text-on-surface">Nama</label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary">
        @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="email" class="block text-sm font-bold text-on-surface">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary">
        @error('email') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="password" class="block text-sm font-bold text-on-surface">Password</label>
        <input type="password" name="password" id="password" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary">
        @error('password') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-bold text-on-surface">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary">
    </div>
    <div>
        <label for="role" class="block text-sm font-bold text-on-surface">Role</label>
        <select name="role" id="role" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:border-primary focus:ring-primary">
            <option value="petugas" @selected(old('role', isset($user) && $user->hasRole('petugas') ? 'petugas' : '') == 'petugas')>Petugas</option>
            <option value="admin" @selected(old('role', isset($user) && $user->hasRole('admin') ? 'admin' : '') == 'admin')>Admin</option>
        </select>
        @error('role') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
