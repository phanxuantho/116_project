<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolYearAndSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dữ liệu bạn đã cung cấp
        $data = [
            ['semester' => 1, 'school_year' => '2021-2022', 'start_date' => '08/08/2021', 'end_date' => '06/01/2022'],
            ['semester' => 2, 'school_year' => '2021-2022', 'start_date' => '09/01/2022', 'end_date' => '23/06/2022'],
            ['semester' => 1, 'school_year' => '2022-2023', 'start_date' => '08/08/2022', 'end_date' => '06/01/2023'],
            ['semester' => 2, 'school_year' => '2022-2023', 'start_date' => '09/01/2023', 'end_date' => '23/06/2023'],
            ['semester' => 1, 'school_year' => '2023-2024', 'start_date' => '07/08/2023', 'end_date' => '29/12/2023'],
            ['semester' => 2, 'school_year' => '2023-2024', 'start_date' => '01/01/2024', 'end_date' => '21/06/2024'],
            ['semester' => 1, 'school_year' => '2024-2025', 'start_date' => '05/08/2024', 'end_date' => '03/01/2025'],
            ['semester' => 2, 'school_year' => '2024-2025', 'start_date' => '06/01/2025', 'end_date' => '20/06/2025'],
            ['semester' => 1, 'school_year' => '2025-2026', 'start_date' => '04/08/2025', 'end_date' => '02/01/2026'],
            ['semester' => 2, 'school_year' => '2025-2026', 'start_date' => '05/01/2026', 'end_date' => '19/06/2026'],
        ];

        // Xóa dữ liệu cũ để tránh trùng lặp
        DB::table('116_semesters')->delete();
        DB::table('116_school_years')->delete();
        
        // Reset lại auto-increment
        DB::statement('ALTER TABLE 116_school_years AUTO_INCREMENT = 1;');
        DB::statement('ALTER TABLE 116_semesters AUTO_INCREMENT = 1;');


        $schoolYears = [];

        foreach ($data as $item) {
            // Thêm năm học nếu chưa tồn tại
            if (!isset($schoolYears[$item['school_year']])) {
                $schoolYearId = DB::table('116_school_years')->insertGetId([
                    'name' => $item['school_year'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $schoolYears[$item['school_year']] = $schoolYearId;
            }

            // Thêm học kỳ
            DB::table('116_semesters')->insert([
                'school_year_id' => $schoolYears[$item['school_year']],
                'semester_number' => $item['semester'],
                'name' => 'Học kỳ ' . $item['semester'] . ' năm học ' . $item['school_year'],
                'start_date' => Carbon::createFromFormat('d/m/Y', $item['start_date'])->format('Y-m-d'),
                'end_date' => Carbon::createFromFormat('d/m/Y', $item['end_date'])->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Cập nhật ngày bắt đầu và kết thúc cho năm học
        foreach ($schoolYears as $yearName => $yearId) {
            $semesters = DB::table('116_semesters')->where('school_year_id', $yearId)->get();
            if ($semesters->isNotEmpty()) {
                $startDate = $semesters->min('start_date');
                $endDate = $semesters->max('end_date');
                
                DB::table('116_school_years')->where('id', $yearId)->update([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
    }
}
