<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">
            <x-filter toggle="Filter" :fields="$fields" />
        </x-form>

        <x-form method="POST" action="{{ moduleRoute('getTable') }}">

            <x-action/>

            <div class="container">
                <div class="table-responsive" id="table_data">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="9" class="center">
                                    <input class="btn-check-d" type="checkbox">
                                </th>
                                <th class="text-center column-action">{{ __('Action') }}</th>
                                <th class="text-center column-checkbox">{{ __('No.') }}</th>
                                @foreach($fields as $value)
                                    <th {{ Template::extractColumn($value) }}>
                                        @if($value->sort)
                                            @sortablelink($value->code, __($value->name))
                                            @else
                                                {{ __($value->name) }}
                                            @endif
                                    </th>
                                @endforeach
                                <th>{{ __('Total Linen') }}</th>
                                <th>{{ __('Kurang Linen') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key => $table)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox" name="code[]"
                                            value="{{ $table->field_primary }}">
                                    </td>
                                    <td class="col-md-2 text-center column-action">
                                        <x-crud :model="$table" />
                                    </td>
                                    <td>{{ iteration($data, $key) }}</td>
                                    <td>{{ $table->field_rs_name ?? '' }}</td>
                                    <td>{{ $table->field_category_name ?? '' }}</td>
                                    <td>{{ $table->field_name }}</td>
                                    <td>{{ $table->field_weight }}</td>
                                    <td>{{ $table->field_parstock }}</td>
                                    <td>{{ $table->field_total ?? 0 }}</td>
                                    <td>{{ $table->field_parstock - $table->field_total ?? 0 }}</td>
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