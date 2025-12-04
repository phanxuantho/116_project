<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Kết quả Học tập</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 13px; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; vertical-align: middle; }
        th { font-weight: bold; background-color: #f0f0f0; }
        .text-left { text-align: left; }
        .header-table { border: none; margin-bottom: 20px; }
        .header-table td { border: none; padding: 0; text-align: center; }
        .title { font-weight: bold; text-transform: uppercase; margin: 5px 0; font-size: 14px; text-align: center; }
        
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

    @foreach($data as $provinceName => $students)
    <div class="page-break">
        <table class="header-table">
            <tr>
                <td style="width: 40%;">BỘ GIÁO DỤC VÀ ĐÀO TẠO<br><b>TRƯỜNG ĐẠI HỌC TÂY NGUYÊN</b></td>
                <td style="width: 60%;"><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br>Độc lập - Tự do - Hạnh phúc</td>
            </tr>
        </table>

        <div class="title">KẾT QUẢ HỌC TẬP VÀ RÈN LUYỆN HỌC KỲ {{ $meta['semester'] }} NĂM HỌC {{ $meta['year_name'] }}</div>
        <div class="title">CỦA SINH VIÊN ĐƯỢC CẤP HỖ TRỢ TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, <br> CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH {{ mb_strtoupper($provinceName) }}</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>Họ và tên</th>
                    <th>MSSV</th>
                    <th>Lớp/Năm tuyển sinh</th>
                    <th>Kết quả<br>học tập</th>
                    <th>Kết quả<br>rèn luyện</th>
                    <th>Hộ khẩu TT</th>
                    <th>Tỉnh/Thành phố</th>
                    <th>Số CCCD</th>
                    <th>Điện thoại</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                @php $result = $student->academicResults->first(); @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->full_name }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td>{{ $student->class->class_name ?? '' }}<br>({{ $student->class->course_year ?? '' }})</td>
                    <td>{{ $result ? $result->academic_score : '' }}</td>
                    <td>{{ $result ? $result->conduct_score : '' }}</td>
                    <td class="text-left">{{ $student->old_address_detail }}</td>
                    <td>{{ $provinceName }}</td>
                    <td>{{ $student->citizen_id_card }}</td>
                    <td>{{ $student->phone }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px;"><b>Danh sách gồm: {{ $students->count() }} sinh viên.</b></div>

        <table class="header-table" style="margin-top: 30px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <i>........, ngày ...... tháng ...... năm ......</i><br>
                    <b>HIỆU TRƯỞNG</b><br><br><br><br><b>(Đã ký)</b>
                </td>
            </tr>
        </table>
    </div> 
    @endforeach
</body>
</html>