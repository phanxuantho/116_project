<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Danh sách Rà soát</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 13px; }
        .container { width: 100%; margin: 0 auto; }
        .header, .title, .footer { text-align: center; }
        .header table, .title table { width: 100%; }
        .header td { font-weight: bold; }
        .title h2 { margin: 5px 0; font-size: 16px; }
        .title h3 { margin: 5px 0; font-size: 14px; font-style: italic; }
        .class-info { text-align: left; margin: 20px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid black; padding: 5px; text-align: left; }
        table.data th { text-align: center; font-weight: bold; }
        @media print {
            @page { size: A4 landscape; margin: 20mm; }
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td style="text-align: center;">TRƯỜNG ĐẠI HỌC TÂY NGUYÊN<br>KHOA {{ strtoupper($facultyName) }}</td>
                    <td style="text-align: center;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>Độc lập - Tự do - Hạnh phúc</td>
                </tr>
            </table>
        </div>
        <div class="title">
            <h2>DANH SÁCH RÀ SOÁT CẤP HỖ TRỢ HỌC PHÍ, CHI PHÍ SINH HOẠT</h2>
            <h2>ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP</h2>
            <h3>THÁNG {{ $month }} HỌC KỲ {{ $semester }} NĂM HỌC {{ $school_year }}</h3>
        </div>
        <div class="class-info">
            <strong>Lớp:</strong> {{ $className }}
        </div>
        <table class="data">
            <thead>
                <tr>
                    <th rowspan="2">STT</th>
                    <th rowspan="2">Họ và tên</th>
                    <th rowspan="2">MSSV</th>
                    <th rowspan="2">Lớp</th>
                    <th rowspan="2">Khoa</th>
                    <th rowspan="2">Ngân hàng</th>
                    <th rowspan="2">Số tài khoản</th>
                    <th colspan="2">Học kỳ {{ $semester }}<br>Năm học {{ $school_year }}</th>
                    <th rowspan="2">Ký tên</th>
                    <th rowspan="2">Ghi chú</th>
                </tr>
                <tr>
                    <th>Dừng nhận</th>
                    <th>Tiếp tục nhận</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $student)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->class->class_name ?? '' }}</td>
                        <td>{{ $student->class->faculty->faculty_name ?? '' }}</td>
                        <td>{{ $student->bank_name }}</td>
                        <td>{{ $student->bank_account }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="footer" style="margin-top: 20px;">
            <p>Danh sách gồm {{ count($students) }} sinh viên.</p>
        </div>
    </div>
</body>
</html>