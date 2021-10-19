@extends('admin.master')

@section('meta:title', 'Scholars')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <scholar-type-card
            slp-data-fetch-url="{{ route('admin.users.cards-data') }}"
            axie-count-fetch-url="{{ route('admin.users.axie-count-data') }}"
        >
        </scholar-type-card>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card card-gray card-outline ">
            <div class="card-header p-2">
                <div class="row">
                    <div class="col-md-10">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a @click="initList('table-1')" class="nav-link active" data-target="#tab1" href="javascript:void(0)" data-toggle="tab"><i class="fas fa-users"></i> Active</a></li>
                            <li class="nav-item"><a @click="initList('table-2')" class="nav-link" data-target="#tab2" href="javascript:void(0)" data-toggle="tab"><i class="fas fa-user-times"></i> Archive</a></li>
                            <li class="nav-item"><a @click="initGraph" class="nav-link" data-target="#tab3" href="javascript:void(0)" data-toggle="tab"><i class="fas fa-chart-line"></i> Graphs</a></li>
                            <li class="nav-item"><a @click="$refs['axie-list'].initData()" class="nav-link" data-target="#tab4" href="javascript:void(0)" data-toggle="tab"><i class="fas fa-paw"></i> Axies</a></li>
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
                        <user-table
                        ref="table-1"
                        sync-battle-logs-url="{{ route('admin.users.update-battle-logs') }}"
                        create-url="{{ route('admin.users.create') }}"
                        export-url="{{ route('admin.users.export') }}"
                        fetch-url="{{ route('admin.users.fetch') }}"
                        ></user-table>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <user-table
                        ref="table-2"
                        disabled
                        fetch-url="{{ route('admin.users.fetch-archive') }}"
                        ></user-table>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <div class="row">
                            <div class="col-md-12">
                                <slp-graph
                                    line-url="{{ route('admin.axie-analytics.fetch-graph-line.user') }}"
                                    pie-url="{{ route('admin.axie-analytics.fetch-graph.user') }}"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab4">
                        <scholar-axie-list ref="axie-list" fetch-url="{{ route('admin.users.lists') }}"></scholar-axie-list>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
