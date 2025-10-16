<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Tổng quan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header, .title, .footer { text-align: center; }
        .header table { width: 100%; }
        .header td { font-weight: bold; }
        .title h2 { margin: 15px 0 5px 0; font-size: 14px; text-transform: uppercase; }
        .title h3 { margin: 5px 0; font-size: 12px; font-style: italic; font-weight: normal; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.data th, table.data td { border: 1px solid black; padding: 5px; text-align: left; }
        table.data th { text-align: center; font-weight: bold; }
        .faculty-row td { font-weight: bold; }
        .course-row td { font-style: italic; padding-left: 20px !important; }
        .total-row td { font-weight: bold; }
        .grand-total-row td { font-weight: bold; background-color: #f2f2f2; }
        .summary-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .summary-table td, .summary-table th { border: 1px solid black; padding: 5px; }
        .signature-section { margin-top: 10px; width: 100%; }
        .signature-section td { text-align: center; font-weight: bold; width: 50%; }
        @media print {
            @page { size: A4 landscape; margin: 20mm; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td style="text-align: center;">TRƯỜNG ĐẠI HỌC TÂY NGUYÊN<br>PHÒNG CÔNG TÁC SINH VIÊN</td>
                    <td style="text-align: center;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>Độc lập - Tự do - Hạnh phúc</td>
                </tr>
            </table>
        </div>

        <div class="title">
            <h2>BẢNG TỔNG HỢP THEO DÕI TÌNH HÌNH SINH VIÊN NHẬN 116</h2>
            <h3>{{ $statistic_time }}</h3>
        </div>

        <table class="data">
            <thead>
                <tr>
                    <th>TT</th>
                    <th>Mã lớp</th>
                    <th>Tên lớp</th>
                    <th>Sĩ số</th>
                    <th>Tổng số đăng ký nhận</th>
                    <th>Dừng cấp</th>
                    <th>Tạm dừng cấp</th>
                    <th>Đang cấp</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faculties as $faculty)
                    <tr class="faculty-row">
                        <td colspan="9">Khoa: {{ $faculty->faculty_name }}</td>
                    </tr>
                    @php
                        $classesByCourse = $faculty->classes->groupBy('course_year');
                    @endphp
                    @foreach ($classesByCourse as $courseYear => $classes)
                        <tr class="course-row">
                            <td colspan="9">Khóa: {{ $courseYear }}</td>
                        </tr>
                        @php $stt = 1; @endphp
                        @foreach ($classes as $class)
                            @php
                                $stats = $studentStats->get($class->id);
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $stt++ }}</td>
                                <td>{{ $class->class_code }}</td>
                                <td>{{ $class->class_name }}</td>
                                <td style="text-align: center;">{{ $class->class_size ?? 0 }}</td>
                                <td style="text-align: center;">{{ $stats->total_registered ?? 0 }}</td>
                                <td style="text-align: center;">{{ $stats->total_stopped ?? 0 }}</td>
                                <td style="text-align: center;">{{ $stats->total_paused ?? 0 }}</td>
                                <td style="text-align: center;">{{ $stats->total_receiving ?? 0 }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                    @php
                        $totals = $facultyTotals[$faculty->id];
                    @endphp
                    <tr class="grand-total-row">
                        <td colspan="3" style="text-align: right; padding-right: 10px;">Tổng cộng khoa {{ $faculty->faculty_name }}:</td>
                        <td style="text-align: center;">{{ $totals->enrolled }}</td>
                        <td style="text-align: center;">{{ $totals->registered }}</td>
                        <td style="text-align: center;">{{ $totals->stopped }}</td>
                        <td style="text-align: center;">{{ $totals->paused }}</td>
                        <td style="text-align: center;">{{ $totals->receiving }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tổng kết khóa và toàn trường --}}
        <table class="summary-table">
            <thead>
                <tr>
                    <th style="width: 40%;"> <strong>Tổng hợp theo khoá</strong></th>
                    <th style="width: 12%; text-align: center;">Sĩ số</th>
                    <th style="width: 12%; text-align: center;">Tổng số đăng ký nhận</th>
                    <th style="width: 12%; text-align: center;">Dừng cấp</th>
                    <th style="width: 12%; text-align: center;">Tạm dừng</th>
                    <th style="width: 12%; text-align: center;">Đang cấp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courseTotals as $courseData)
                <tr>
                    <td>Tổng cộng khóa {{ $courseData->course_year }}:</td>
                    <td style="text-align: center;">{{ $courseData->total_enrolled }}</td>
                    <td style="text-align: center;">{{ $courseData->total_registered }}</td>
                    <td style="text-align: center;">{{ $courseData->total_stopped }}</td>
                    <td style="text-align: center;">{{ $courseData->total_paused }}</td>
                    <td style="text-align: center;">{{ $courseData->total_receiving }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Tổng cộng toàn trường:</td>
                    <td style="text-align: center;">{{ $courseTotals->sum('total_enrolled') }}</td>
                    <td style="text-align: center;">{{ $courseTotals->sum('total_registered') }}</td>
                    <td style="text-align: center;">{{ $courseTotals->sum('total_stopped') }}</td>
                    <td style="text-align: center;">{{ $courseTotals->sum('total_paused') }}</td>
                    <td style="text-align: center;">{{ $courseTotals->sum('total_receiving') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: left; margin-top: 10px;">
            <strong><u>Ghi chú:</u></strong>
        </div>

        <table class="signature-section">
            <tr>
                <td>NGƯỜI LẬP BIỂU</td>
                <td>TRƯỞNG PHÒNG</td>
            </tr>
        </table>
    </div>
</body>
</html>