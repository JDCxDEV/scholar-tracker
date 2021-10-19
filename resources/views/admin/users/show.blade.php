@extends('admin.master')

@section('meta:title', $item->renderName())

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $item->renderName() }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $item->renderName() }}</a></li>
                </ol>
            </div>
        </div>
    </section>

    <page-pagination fetch-url="{{ route('admin.users.fetch-pagination', $item->id) }}"></page-pagination>

    <section class="content">
        <user-view
            scholar-daily-slp-fetch-url="{{ route('admin.scholar-daily-records.fetch', ['scholar_id' => $item->id]) }}"
            api-url="{{ route('admin.users.fetch-item', $item->id) }}"
            fetch-url="{{ route('admin.users.fetch-item', $item->id) }}"
            submit-url="{{ route('admin.users.update', $item->id) }}"
            scholar-slp-graph-fetch-url="{{ route('admin.axie-analytics.fetch-slp-line.user', $item->id) }}"
        ></user-view>
    </section>
</div>

@endsection
