<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Cảnh báo Học tập</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 13px; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; vertical-align: middle; }
        th { font-weight: bold; background-color: #f0f0f0; }
        .text-left { text-align: left; }
        .text-red { color: red; font-weight: bold; }
        .header-table { border: none; margin-bottom: 20px; }
        .header-table td { border: none; padding: 0; text-align: center; }
        .title { font-weight: bold; text-transform: uppercase; margin: 5px 0; font-size: 16px; text-align: center; color: #cc0000; }
        
        @media print {
            .page-break { page-break-after: always; }
            .no-print { display: none; }
            @page { size: A4 landscape; margin: 10mm; } 
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="position: fixed; top: 10px; right: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px;">🖨️ In ngay</button>
    </div>

    @php
        $groupedData = $data->groupBy('class.class_name');
    @endphp

    @foreach($groupedData as $className => $students)
    <div class="page-break">
        <table class="header-table">
            <tr>
                <td style="width: 40%;">TRƯỜNG ĐẠI HỌC TÂY NGUYÊN<br><b>PHÒNG CÔNG TÁC SINH VIÊN</b></td>
                <td style="width: 60%;"><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br>Độc lập - Tự do - Hạnh phúc</td>
            </tr>
        </table>

        <div class="title">DANH SÁCH SINH VIÊN CẢNH BÁO KẾT QUẢ HỌC TẬP <br> HỌC KỲ {{ $meta['semester'] }} - NĂM HỌC {{ $meta['year'] }}</div>
        <div style="text-align: center; font-weight: bold; margin-bottom: 15px; font-style: italic;">Lớp: {{ $className }}</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>MSSV</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>TC ĐK</th>
                    <th>TC TL</th>
                    <th>Điểm TB</th>
                    <th>Xếp loại</th>
                    <th>Điểm RL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                @php 
                    $result = $student->academicResults->first(); 
                    $score = $result ? $result->academic_score : 0;
                    $rank = ($score < 1.0) ? 'Kém' : 'Yếu';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td class="text-left">{{ $student->full_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                    <td>{{ $result->registered_credits ?? '' }}</td>
                    <td>{{ $result->accumulated_credits ?? '' }}</td>
                    <td class="text-red">{{ $score }}</td>
                    <td>{{ $rank }}</td>
                    <td>{{ $result->conduct_score ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px;"><b>Tổng số: {{ $students->count() }} sinh viên.</b></div>

        <table class="header-table" style="margin-top: 30px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <i>........, ngày ...... tháng ...... năm ......</i><br>
                    <b>TL. HIỆU TRƯỞNG</b><br><br><br><br><b>(Đã ký)</b>
                </td>
            </tr>
        </table>
    </div> 
    @endforeach
</body>
</html>