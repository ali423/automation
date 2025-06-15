@extends('layouts.main')
@section('title', 'ویرایش واحد')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">ویرایش واحد</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('unit.update', $unit) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="name">نام</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="symbol">نماد</label>
                                <input type="text" class="form-control @error('symbol') is-invalid @enderror" 
                                       id="symbol" name="symbol" value="{{ old('symbol', $unit->symbol) }}" required>
                                @error('symbol')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">بروزرسانی</button>
                                <a href="{{ route('unit.index') }}" class="btn btn-secondary">انصراف</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 