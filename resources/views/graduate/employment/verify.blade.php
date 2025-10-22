<x-guest-layout> <div class="mb-4 text-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Cổng Khai Báo Việc Làm Sinh Viên Tốt Nghiệp
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Vui lòng nhập Mã sinh viên và Số CCCD để xác thực và khai báo.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

     @if ($errors->has('message'))
         <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
             {{ $errors->first('message') }}
         </div>
     @endif


    <form method="POST" action="{{ route('graduate.employment.verify.post') }}">
        @csrf

        <div>
            <x-input-label for="student_code" value="Mã sinh viên (*)" />
            <x-text-input id="student_code" class="block mt-1 w-full" type="text" name="student_code" :value="old('student_code')" required autofocus />
            <x-input-error :messages="$errors->get('student_code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="citizen_id_card" value="Số CCCD (*)" />
            <x-text-input id="citizen_id_card" class="block mt-1 w-full"
                            type="password"
                            name="citizen_id_card"
                            required />
            <x-input-error :messages="$errors->get('citizen_id_card')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                Xác thực và Khai báo
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>