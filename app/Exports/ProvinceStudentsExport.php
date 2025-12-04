<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProvinceStudentsExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $year;

    public function __construct($data, $year)
    {
        $this->data = $data;
        $this->year = $year;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        // Sử dụng lại chính view in ấn để xuất Excel
        // Lưu ý: Đảm bảo view 'reports.province_students.print' tồn tại và không có lỗi
        return view('reports.province_students.print', [
            'data' => $this->data,
            'year' => $this->year
        ]);
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // In đậm dòng đầu tiên (hoặc điều chỉnh tùy ý)
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}