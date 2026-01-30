@extends('layouts.main')
@section('title', 'ویرایش کالا')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('commodity.update', $commodity) }}"
                              class="needs-validation"
                              novalidate="">
                            @method('PATCH')
                            @csrf
                            @include('dashboard.commodity.partials.form-fields')

                            @include('dashboard.commodity.partials.product-formula')
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('commodity.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('dashboard.commodity.partials.commodity-scripts')
    
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{ asset('js/commodity.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Toggle attribute selection
            $(document).on('click', '.attribute-tag', function() {
                const $btn = $(this);
                const attrId = $btn.data('attribute-id');
                
                if ($btn.hasClass('btn-primary')) {
                    $btn.removeClass('btn-primary').addClass('btn-outline-secondary');
                    $('#selected-attributes-inputs input[value="' + attrId + '"]').remove();
                } else {
                    $btn.removeClass('btn-outline-secondary').addClass('btn-primary');
                    $('#selected-attributes-inputs').append('<input type="hidden" name="attributes[]" value="' + attrId + '">');
                }
                updateSelectedCount();
            });

            // Search attributes
            $('#attribute-search').on('input', function() {
                const q = $(this).val().trim().toLowerCase();
                $('.attribute-tag').each(function() {
                    const name = $(this).data('name').toString().toLowerCase();
                    $(this).toggle(name.includes(q));
                });
            });

            // Clear all selections
            $('#clear-all-attributes').click(function() {
                $('.attribute-tag').removeClass('btn-primary').addClass('btn-outline-secondary');
                $('#selected-attributes-inputs').empty();
                updateSelectedCount();
            });

            // Update selected count
            function updateSelectedCount() {
                const count = $('#selected-attributes-inputs input').length;
                $('#selected-count').text(count + ' انتخاب شده');
            }

            // Initial count
            updateSelectedCount();
        });
    </script>
@endsection
