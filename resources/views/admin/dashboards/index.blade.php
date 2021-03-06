@extends('admin.master')

@section('meta:title', 'Dashboard')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
        </div>
    </section>

    <section class="content">
        <dashboard-analytics
        title="User Analytics"
        ></dashboard-analytics>
    </section>
</div>

@endsection
