<style>
    .read-only-badge {
        display: inline-block;
        padding: 6px 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .search-box {
        margin-bottom: 20px;
    }

    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 300px;
        font-size: 14px;
    }

    .btn-search {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
        text-decoration: none;
        display: inline-block;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }

    th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
    }
</style>

<div class="card">
    <span class="read-only-badge">📊 View Only - Read-Only Access</span>
    
    @php
        // Ambil tanggal posisi data terbaru berdasarkan model yang digunakan
        $latestTanggalPosisi = null;
        $modelClass = get_class($data->first() ?? new stdClass());
        
        if ($data->count() > 0) {
            try {
                $model = $modelClass::query();
                
                // Filter berdasarkan KC untuk manager/rmft
                if (auth()->user()->role === 'manager' || auth()->user()->role === 'rmft') {
                    $kcField = null;
                    $kcValue = auth()->user()->kode_kanca ?? auth()->user()->nama_kanca ?? '';
                    
                    // Cek field yang tersedia untuk filter KC
                    $firstItem = $data->first();
                    if (isset($firstItem->kode_kc)) {
                        $kcField = 'kode_kc';
                    } elseif (isset($firstItem->kd_cabang)) {
                        $kcField = 'kd_cabang';
                    } elseif (isset($firstItem->kode_cabang)) {
                        $kcField = 'kode_cabang';
                    }
                    
                    if ($kcField && $kcValue) {
                        $model->where($kcField, $kcValue);
                    }
                }
                
                // Cari field tanggal posisi data
                $dateField = null;
                $firstItem = $data->first();
                if (isset($firstItem->tanggal_posisi_data)) {
                    $dateField = 'tanggal_posisi_data';
                } elseif (isset($firstItem->tgl_posisi_data)) {
                    $dateField = 'tgl_posisi_data';
                } elseif (isset($firstItem->posisi_data)) {
                    $dateField = 'posisi_data';
                }
                
                if ($dateField) {
                    $latestTanggalPosisi = $model->whereNotNull($dateField)
                                                 ->orderBy($dateField, 'desc')
                                                 ->first();
                }
            } catch (\Exception $e) {
                // Jika terjadi error, abaikan saja
            }
        }
    @endphp
    
    @if($latestTanggalPosisi && isset($dateField) && $latestTanggalPosisi->{$dateField})
        <div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px;">
            <p style="margin: 0; color: #1976d2; font-weight: 600; font-size: 14px;">
                📅 Posisi Data: {{ \Carbon\Carbon::parse($latestTanggalPosisi->{$dateField})->format('d F Y') }}
            </p>
        </div>
    @endif
    
    <div class="search-box">
        <form action="{{ $route }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <input type="text" name="search" placeholder="Cari norek, nama, atau PN..." value="{{ request('search') }}">
            
            <select name="year" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; min-width: 120px;">
                <option value="">Semua Tahun</option>
                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
            </select>
            
            <select name="month" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; min-width: 140px;">
                <option value="">Semua Bulan</option>
                @php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    foreach ($months as $num => $name) {
                        $selected = request('month') == $num ? 'selected' : '';
                        echo "<option value=\"$num\" $selected>$name</option>";
                    }
                @endphp
            </select>
            
            <button type="submit" class="btn-search">🔍 Cari</button>
            @if(request('search') || request('year') || request('month'))
                <a href="{{ $route }}" class="btn-search" style="background: #6c757d;">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        @if($data->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        @php
                            $firstItem = $data->first();
                            $columns = array_keys($firstItem->getAttributes());
                            // Kolom yang disembunyikan untuk non-admin (hanya no rekening, CIF tetap tampil)
                            $hiddenColumns = ['id', 'created_at', 'updated_at'];
                            if (!auth()->user()->isAdmin()) {
                                $hiddenColumns = array_merge($hiddenColumns, ['no_rekening', 'norekening', 'nomor_rekening', 'norek']);
                            }
                        @endphp
                        @foreach($columns as $column)
                            @if(!in_array($column, $hiddenColumns))
                                <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $item)
                        <tr>
                            <td>{{ $data->firstItem() + $index }}</td>
                            @foreach($columns as $column)
                                @if(!in_array($column, $hiddenColumns))
                                    <td>
                                        @php
                                            $value = $item->{$column};
                                            // Check if value is numeric
                                            if (in_array($column, ['saldo', 'nominal', 'amount', 'dpk', 'aum']) && is_numeric($value)) {
                                                echo number_format($value, 0, ',', '.');
                                            } else {
                                                echo $value;
                                            }
                                        @endphp
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data ditemukan untuk KC Anda.</p>
            </div>
        @endif
    </div>

    @if($data->hasPages())
        <div class="pagination-wrapper">
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif
</div>
