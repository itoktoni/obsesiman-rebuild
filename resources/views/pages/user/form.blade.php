<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)
                <x-form-input col="6" name="name" />
                <x-form-input col="6" name="username" />
                <x-form-input col="6" name="phone" />
                <x-form-input col="6" name="email" />
                <x-form-select col="6" class="search" name="role" :options="$roles" />
                <x-form-input col="6" name="password" type="password"/>
            @endbind

        </x-form>
    </x-card>
</x-layout>
