@extends('layouts.main')
@section('title', 'لیست درخواست های خرید کالا')
@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">

@endsection

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">لیست درخواست های خرید کالا</h4>
                    
                    {{-- Pagination Controls --}}
                    <x-pagination-controls :paginator="$requests" :options="$options" />
                    
                    {{-- Importing Request Table --}}
                    @include('dashboard.processes.importing-request.partials.importing-request-table')
                    
                    {{-- Pagination Navigation --}}
                    <x-pagination-navigation :paginator="$requests" />

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
@endsection

@section('page_scripts')
    {{-- Server-side pagination doesn't need DataTables scripts --}}
@endsection
