<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Danh sách Sinh viên</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 13px; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; vertical-align: middle; }
        th { font-weight: bold; background-color: #f0f0f0; }
        .text-left { text-align: left; }
        .header-table { border: none; margin-bottom: 20px; }
        .header-table td { border: none; padding: 0; text-align: center; }
        .title { font-weight: bold; text-transform: uppercase; margin-top: 15px; margin-bottom: 5px; font-size: 14px; text-align: center; }
        
        /* CSS ngắt trang khi in */
        @media print {
            .page-break { page-break-after: always; }
            .no-print { display: none; }
            @page { size: A4 landscape; margin: 10mm; } /* In ngang khổ A4 */
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="position: fixed; top: 10px; right: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">🖨️ In ngay</button>
    </div>

    @foreach($data as $provinceName => $students)
    <div class="page-break">
        
        {{-- Header Quốc Hiệu --}}
        <table class="header-table">
            <tr>
                <td style="width: 40%;">
                    BỘ GIÁO DỤC VÀ ĐÀO TẠO<br>
                    <b>TRƯỜNG ĐẠI HỌC TÂY NGUYÊN</b>
                </td>
                <td style="width: 60%;">
                    <b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</b><br>
                    Độc lập - Tự do - Hạnh phúc
                </td>
            </tr>
        </table>

        {{-- Tiêu đề Báo cáo --}}
        <div class="title">DANH SÁCH SINH VIÊN KHOA TUYỂN SINH {{ $year }} ĐƯỢC CẤP HỖ TRỢ TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT</div>
        <div class="title">ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH {{ mb_strtoupper($provinceName) }}</div>

        {{-- Bảng dữ liệu --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>HỌ VÀ TÊN</th>
                    <th>MSSV</th>
                    <th>LỚP/NĂM TUYỂN SINH</th>
                    <th>KHOA</th>
                    <th>Hộ khẩu TT<br>(Xã, Huyện)</th>
                    <th>Tỉnh</th>
                    <th>CCCD</th>
                    <th>Điện thoại</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->full_name }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td>
                        {{ $student->class->class_name ?? '' }}<br>
                        ({{ $student->class->course_year ?? '' }})
                    </td>
                    <td>{{ $student->class->faculty->faculty_name ?? '' }}</td>
                    <td class="text-left">
                        {{ $student->address_detail }} - {{ $student->ward->name ?? '' }}
                    </td>
                    <td>{{ $provinceName }}</td>
                    <td>{{ $student->citizen_id_card }}</td>
                    <td>{{ $student->phone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px;">
            <b>Tổng số: {{ $students->count() }} sinh viên.</b>
        </div>

        {{-- Chữ ký --}}
        <table class="header-table" style="margin-top: 30px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <i>........, ngày ...... tháng ...... năm ......</i><br>
                    <b>HIỆU TRƯỞNG</b><br>
                    <br><br><br>
                    <b>(Đã ký)</b>
                </td>
            </tr>
        </table>

    </div> 
    @endforeach

</body>
</html>