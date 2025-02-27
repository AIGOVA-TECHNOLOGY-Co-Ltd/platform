@extends('layouts.in')

@section('body')
    <div class="overflow-auto scroll-visible header-sticky">
        <table id="enterprise-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap">
            <thead>
            <tr>
                <th class="w-1">{{__('enterprise-index.no')}}</th>
                <th class="w-1">{{__('enterprise-index.name')}}</th>
                <th class="w-1">{{__('enterprise-index.email')}}</th>
                <th class="w-1">{{__('enterprise-index.role')}}</th>
                <th class="w-1">{{__('enterprise-index.enterpriseName')}}</th>
                <th class="w-1">{{__('enterprise-index.action')}}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($data as $index =>$row)
                <tr>
                    <td>{{$index+1}}</td>
                    <td>{{$row['user_name']}}</td>
                    <td>{{$row['email']}}</td>
                    <td>{{$row['roleName']}}</td>
                    <td>{{$row['name']}}</td>
                    <td>
                        <a class="btn btn-primary"
                           href="{{route('enterprise.show',['id'=> $row['id']])}}">{{__('enterprise-index.edit-button')}}</a>
                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                           class="btn btn-outline-danger mr-5">{{__('enterprise-index.delete-button')}}</a>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
    @include('molecules.delete-modal', [
        'route' => route('enterprise.destroy', ['id' => $row['id']]),
        'title' => __('enterprise-index.delete-title'),
        'message' => __('enterprise-index.delete-message'),
    ])
@stop
