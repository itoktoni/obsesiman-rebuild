<x-layout>

    <x-card>

        <x-form method="GET" action="{{ moduleRoute('getTable') }}">
            <x-filter toggle="Filter" hide="true" :fields="$fields" />
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
                                <th style="width: 100px" class="text-center column-action">{{ __('Action') }}</th>
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
                                <th class="text-center">{{ __('Pemakaian') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $key => $table)
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
                                    <td>{{ iteration($data, $key) }}</td>
                                    <td>{{ $table->field_primary }}</td>
                                    <td>{{ $table->field_rs_name }}</td>
                                    <td>{{ $table->field_ruangan_name }}</td>
                                    <td>{{ $table->field_name }}</td>
                                    <td>{{ $table->field_weight }} Kg</td>
                                    <td>{{ $table->field_status_register_name }}</td>
                                    <td>{{ $table->field_status_cuci_name }}</td>
                                    <td>{{ $table->field_status_transaction_name }}</td>
                                    <td>{{ $table->field_status_process_name }}</td>
                                    <td>{{ $table->field_updated_at }}</td>
                                    <td>{{ $table->field_total_cuci }}</td>
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