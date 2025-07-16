<x-layouts.admin-layout>
    <x-slot name="header">User Management &gt; Add User</x-slot>

    <div class="flex min-h-[70vh] items-center justify-center">
        <form class="w-full max-w-2xl space-y-8 rounded-lg border border-primary/20 bg-white p-10" method="POST"
            enctype="multipart/form-data" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="flex flex-col items-center">
                <x-upload-avatar-toggle />
            </div>

            <div>
                <x-input-label for="nomor_induk" value="NISN/NIP" />
                <x-text-input id="nomor_induk" name="nomor_induk" type="text" class="mt-1" required type="number"
                    placeholder="Enter NISN/NIP" :value="old('nomor_induk')" />
                <x-input-error :messages="$errors->get('nomor_induk')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="first_name" value="Name" />
                <x-text-input id="first_name" name="name" type="text" class="mt-1" required
                    placeholder="Enter first name" :value="old('name')" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="jk" value="Jenis Kelamin" />
                <select name="jk" id="jk" class="w-full">
                    <option value="" disabled selected hidden>Pilih Jenis Kelamin</option>
                    <option value="Laki-Laki" @selected(old('jk') === 'Laki-Laki')>Laki-laki</option>
                    <option value="Perempuan" @selected(old('jk') === 'Perempuan')>Perempuan</option>
                </select>

                <x-input-error :messages="$errors->get('jk')" class="mt-2" />
            </div>

            <div class="">
                <x-input-label for="address" value="Address" />
                <x-text-input id="address" name="address" type="text" class="mt-1" required
                    placeholder="Enter address" :value="old('address')" />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="">
                <x-input-label for="email" value="Email Address" />
                <x-text-input id="email" name="email" type="email" class="mt-1" required
                    placeholder="Enter email address" :value="old('email')" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="">
                <x-input-label value="Role" />
                <div class="mt-2 flex flex-col space-y-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="admin"
                            class="form-radio text-primary focus:ring-primary" required @checked(old('role') === 'admin') />
                        <span class="ml-2">Administrator</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="teacher"
                            class="form-radio text-primary focus:ring-primary" @checked(old('role') === 'teacher') />
                        <span class="ml-2">Teacher</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="user"
                            class="form-radio text-primary focus:ring-primary" @checked(old('role') === 'user') />
                        <span class="ml-2">Student</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
            <div class="">
                <x-upload-avatar-toggle type="toggle" />
            </div>
            <div class="">
                <x-input-label for="password" value="Password" />
                <x-password-input id="password" name="password" required placeholder="Enter password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-password-input id="password_confirmation" name="password_confirmation" required
                    placeholder="Confirm password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Add User</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin-layout>
