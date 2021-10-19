@extends('admin.master')

@section('meta:title', 'Users')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Scholar Type</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Scholar Type</a></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card card-gray card-outline ">
            <div class="card-header p-2">
                <div class="row">
                    <div class="col-md-10">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a @click="initList('table-1')" class="nav-link active" data-target="#tab1" href="javascript:void(0)" data-toggle="tab">Active</a></li>
                            <li class="nav-item"><a @click="initList('table-2')" class="nav-link" data-target="#tab2" href="javascript:void(0)" data-toggle="tab">Archive</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <button  @click="refresh" type="button" class="btn btn-light btn-rounded border float-right mr-2"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="tab1">
                        <scholar-type-table
                        ref="table-1"
                        create-url="{{ route('admin.scholar-types.create') }}"
                        fetch-url="{{ route('admin.scholar-types.fetch') }}"
                        ></scholar-type-table>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <scholar-type-table
                        ref="table-2"
                        disabled
                        fetch-url="{{ route('admin.scholar-types.fetch-archive') }}"
                        ></scholar-type-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
