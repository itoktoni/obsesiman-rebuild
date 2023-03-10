<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">
            <x-filter toggle="Filter" :fields="$fields" />
        </x-form>

        <x-form method="POST" action="{{ moduleRoute('getTable') }}">

            <x-action/>

            <div class="container">
                <div class="table-responsive" id="table_data">
                    <table class="table table-bordered table-striped overflow">
                        <thead>
                            <tr>
                                <th width="9" class="center">
                                    <input class="btn-check-d" type="checkbox">
                                </th>
                                <th style="width: 150px" class="text-center column-action">{{ __('Action') }}</th>
                                @foreach($fields as $value)
                                    <th {{ Template::extractColumn($value) }}>
                                        @if($value->sort)
                                            @sortablelink($value->code, __($value->name))
                                            @else
                                                {{ __($value->name) }}
                                            @endif
                                    </th>
                                @endforeach
                                <th class="text-center">{{ __('Kotor') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $table)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" name="code[]"
                                            value="{{ $table->field_primary }}">
                                    </td>
                                    <td class="col-md-3 text-center column-action">
                                        <x-crud :model="$table">
                                            <x-button module="getHistory" key="{{ $table->field_primary }}" color="success"
                                                icon="book" />
                                        </x-crud>
                                    </td>
                                    <td>{{ $table->field_primary }}</td>
                                    <td>{{ $table->field_rs_name }}</td>
                                    <td>{{ $table->field_ruangan_name }}</td>
                                    <td>{{ $table->field_name }}</td>
                                    <td>{{ $table->field_stock_status }}</td>
                                    <td>{{ $table->field_last_status }}</td>
                                    <td class="text-center">{{ $table->kotor ?? 0 }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :data="$data" />
            </div>

        </x-form>

    </x-card>

</x-layout>