<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Tốt nghiệp TK03</title>
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

        <div class="title">KẾT QUẢ TỐT NGHIỆP CỦA SINH VIÊN ĐƯỢC CẤP HỖ TRỢ TIỀN ĐÓNG HỌC PHÍ, CHI PHÍ SINH HOẠT</div>
        <div class="title">ĐỐI VỚI SINH VIÊN SƯ PHẠM THEO NGHỊ ĐỊNH 116/2020/NĐ-CP, CÓ HỘ KHẨU THƯỜNG TRÚ TẠI TỈNH {{ mb_strtoupper($provinceName) }}</div>
        
        <div style="text-align: center; font-style: italic; margin-bottom: 10px;">
            (Kèm theo Quyết định số: {{ $meta['decision'] }} ngày ......)
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">STT</th>
                    <th rowspan="2">Họ và tên</th>
                    <th rowspan="2">Ngày sinh</th>
                    <th rowspan="2">MSSV</th>
                    <th rowspan="2">Chuyên ngành</th>
                    <th rowspan="2">Điểm TB<br>tích lũy</th>
                    <th rowspan="2">Xếp hạng<br>TN</th>
                    <th rowspan="2">Điểm RL</th>
                    <th rowspan="2">Xếp loại RL</th>
                    <th rowspan="2">Thời gian<br>đào tạo</th>
                    <th rowspan="2">Quyết định số</th>
                    <th colspan="2">Tổng kinh phí đã thụ hưởng</th>
                    <th rowspan="2">Số CCCD</th>
                    <th rowspan="2">Hộ khẩu TT</th>
                    <th rowspan="2">Số điện thoại</th>
                </tr>
                <tr>
                    <th>Học phí</th>
                    <th>Sinh hoạt phí</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                @php $grad = $student->graduation; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->full_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td>{{ $student->class->major->major_name ?? '' }}</td>
                    
                    <td>{{ $grad ? $grad->gpa_final : '' }}</td>
                    <td>{{ $grad ? $grad->graduation_rank : '' }}</td>
                    <td>{{ $grad ? $grad->conduct_score : '' }}</td>
                    <td>{{ $grad ? $grad->conduct_rank : '' }}</td>
                    <td>{{ $grad ? $grad->training_time : '' }}</td>
                    <td>{{ $grad ? $grad->decision_number : '' }}</td>

                    <td>{{ number_format($student->total_tuition) }}</td>
                    <td>{{ number_format($student->total_living) }}</td>

                    <td>{{ $student->citizen_id_card }}</td>
                    <td class="text-left">{{ $student->address_detail }} - {{ $student->ward->name ?? '' }}</td>
                    <td>{{ $student->phone }}</td>
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