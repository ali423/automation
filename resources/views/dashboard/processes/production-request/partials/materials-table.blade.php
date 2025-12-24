{{-- Materials table partial for production request show view --}}
@if($request->materials->count() > 0)
    @if(in_array($request->status, ['approvaled', 'approved']))
        <!-- Show consumed materials for approved requests -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>نام ماده</th>
                        <th>مقدار مصرف شده</th>
                        <th>واحد</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($request->materials as $material)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-cube text-success mr-2"></i>
                                    <strong>{{ $material->title }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="font-weight-bold text-success">
                                    {{ number_format($material->pivot->required_amount, 0) }}
                                </span>
                                <small class="text-muted d-block">
                                    {{ $material->unit ? $material->unit->symbol : '' }}
                                </small>
                            </td>
                            <td>
                                <span class="font-weight-bold">
                                    {{ $material->unit ? $material->unit->name : '-' }}
                                </span>
                                <small class="text-muted d-block">
                                    ({{ $material->unit ? $material->unit->symbol : '' }})
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fa fa-check-circle mr-1"></i>
                                    مصرف شده
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Show current inventory status for pending requests -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>نام ماده</th>
                        <th>مقدار مورد نیاز</th>
                        <th>موجودی</th>
                        <th>تفاوت</th>
                        <th>واحد</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialsWithInventory as $materialData)
                        @php
                            $material = $materialData['material'];
                            $availableStock = $materialData['availableStock'];
                            $conversionInfo = $materialData['conversionInfo'];
                            $allAvailableInfo = $materialData['allAvailableInfo'];
                            $stockStatus = $materialData['stockStatus'];
                            $stockIcon = $materialData['stockIcon'];
                            $statusText = $materialData['stockText'];
                        @endphp
                    
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-cube text-primary mr-2"></i>
                                <strong>{{ $material->title }}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="font-weight-bold text-primary">
                                {{ number_format($material->pivot->required_amount, 0) }}
                            </span>
                            <small class="text-muted d-block">
                                {{ $material->unit ? $material->unit->symbol : '' }}
                            </small>
                        </td>
                        <td>
                            <span class="font-weight-bold text-{{ $stockStatus }}">
                                {{ number_format($availableStock, 0) }}
                            </span>
                            <small class="text-muted d-block">
                                {{ $material->unit ? $material->unit->symbol : '' }}
                            </small>
                        </td>
                        <td>
                            @php
                                $difference = $availableStock - $material->pivot->required_amount;
                                $differenceColor = 'success';
                                $differenceIcon = 'fa-plus';
                                $differenceText = 'مازاد';
                                
                                if ($difference < 0) {
                                    $differenceColor = 'danger';
                                    $differenceIcon = 'fa-minus';
                                    $differenceText = 'کمبود';
                                    $difference = abs($difference);
                                } elseif ($difference == 0) {
                                    $differenceColor = 'warning';
                                    $differenceIcon = 'fa-equals';
                                    $differenceText = 'دقیق';
                                }
                            @endphp
                            <span class="font-weight-bold text-{{ $differenceColor }}">
                                {{ $differenceIcon == 'fa-minus' ? '-' : ($differenceIcon == 'fa-plus' ? '+' : '') }}{{ number_format($difference, 0) }}
                            </span>
                            <small class="text-muted d-block">
                                {{ $differenceText }}
                            </small>
                        </td>
                        <td>
                            <span class="font-weight-bold">
                                {{ $material->unit ? $material->unit->name : '-' }}
                            </span>
                            <small class="text-muted d-block">
                                ({{ $material->unit ? $material->unit->symbol : '' }})
                            </small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $stockStatus }}">
                                <i class="fa {{ $stockIcon }} mr-1"></i>
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
@else
    <div class="text-center py-4">
        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">هیچ ماده اولیه‌ای برای این درخواست تعریف نشده است.</h6>
    </div>
@endif
