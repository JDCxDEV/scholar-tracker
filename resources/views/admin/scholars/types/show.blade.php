@extends('admin.master')

@section('meta:title', $item->renderName())

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $item->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.scholar-types.index') }}">Scholar Types</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $item->name }}</a></li>
                </ol>
            </div>
        </div>
    </section>

    <page-pagination fetch-url="{{ route('admin.scholar-types.fetch-pagination', $item->id) }}"></page-pagination>

    <section class="content">
        <scholar-type-view
        scholars-fetch-url="{{ route('admin.users.fetch', ['scholar_type' => $item->id]) }}"
        fetch-url="{{ route('admin.scholar-types.fetch-item', $item->id) }}"
        submit-url="{{ route('admin.scholar-types.update', $item->id) }}"
        ></scholar-type-view>
    </section>
</div>

@endsection
