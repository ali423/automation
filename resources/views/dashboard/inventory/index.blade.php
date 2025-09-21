@extends('layouts.main')
@section('title', 'لیست موجودی ها')
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
                    <h4 class="card-title mb-2">لیست موجودی ها</h4>
                    
                    {{-- Pagination Controls --}}
                    <x-pagination-controls :paginator="$inventories" :options="$options" />
                    
                    @include('dashboard.inventory.partials.inventory-table')

                </div> <!-- end card body-->
                
                <!-- Pagination Navigation -->
                <div class="card-footer">
                    <x-pagination-navigation :paginator="$inventories" />
                </div>
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
@endsection

@section('page_scripts')
    @include('dashboard.inventory.partials.inventory-scripts')
@endsection 