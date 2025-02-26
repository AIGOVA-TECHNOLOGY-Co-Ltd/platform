@extends('layouts.in')

@section('body')

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <form method="post" action="{{ route('role.feature.update', $row->id) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_action" value="update" />

                @include('domains.role.feature.molecules.create-update')

                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ __('role-feature-update.update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection