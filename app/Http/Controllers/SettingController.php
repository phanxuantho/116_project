<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage; // Thêm Storage
use Illuminate\Support\Facades\Artisan; // Thêm Artisan (cho chế độ bảo trì)
use Illuminate\Support\Facades\Config; // Thêm Config (cho session lifetime)

class SettingController extends Controller
{
    /**
     * Hiển thị trang cấu hình.
     */
    public function edit()
    {
        // Lấy tất cả cấu hình, nhóm theo 'group'
        $settings = Setting::orderBy('group')->orderBy('label')->get()->groupBy('group');
        return view('settings.edit', compact('settings'));
    }

    /**
     * Lưu cấu hình.
     */


     public function update(Request $request)
     {
         $settings = Setting::all();
 
         foreach ($settings as $setting) {
             $key = $setting->key;
             $valueChanged = false; // Dùng cờ (flag) để theo dõi thay đổi
 
             // 1. Xử lý upload file logo
             if ($setting->type === 'file' && $request->hasFile($key)) {
                 // Xóa logo cũ nếu có
                 if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                     Storage::disk('public')->delete($setting->value);
                 }
                 // Lưu logo mới
                 $path = $request->file($key)->store('logos', 'public');
                 // Chỉ lưu nếu đường dẫn mới khác đường dẫn cũ
                 if ($setting->value !== $path) {
                     $setting->value = $path;
                     $setting->save();
                     $valueChanged = $setting->wasChanged('value');
                 }
             } 
             // 2. Xử lý kiểu toggle (Bật/Tắt)
             elseif ($setting->type === 'toggle') {
                  // Nếu key tồn tại trong request (checked), đặt là '1'. 
                  // Nếu không (unchecked), đặt là '0'.
                  $newValue = $request->has($key) ? '1' : '0';
                  
                  // Chỉ cập nhật nếu giá trị thay đổi
                  if ($setting->value !== $newValue) {
                      $setting->value = $newValue;
                      $setting->save();
                      $valueChanged = $setting->wasChanged('value'); // Sẽ là true
                  }
             }
             // 3. Các kiểu input khác (text, textarea, number...)
             elseif ($request->has($key)) { // Kiểm tra xem có giá trị gửi lên không
                  $newValue = $request->input($key);
                  
                  // Chỉ cập nhật nếu giá trị thay đổi
                  if ($setting->value !== $newValue) {
                      $setting->value = $newValue;
                      $setting->save();
                      $valueChanged = $setting->wasChanged('value'); // Sẽ là true
                  }
             }
 
             // === XỬ LÝ ĐẶC BIỆT (CHỈ KHI GIÁ TRỊ THAY ĐỔI) ===
             
             // Chỉ chạy nếu $key là 'maintenance_mode' VÀ giá trị của nó VỪA BỊ THAY ĐỔI
             if ($key === 'maintenance_mode' && $valueChanged) {
                 if ($setting->value == '1') { // Nếu giá trị MỚI là ON
                     Artisan::call('down');
                 } else { // Nếu giá trị MỚI là OFF
                     // Chỉ chạy 'up' nếu nó đang 'down'
                     if (app()->isDownForMaintenance()) {
                          Artisan::call('up');
                     }
                 }
             }
             
             // Xử lý Session (cũng chỉ chạy khi giá trị thay đổi)
             if ($key === 'session_lifetime' && $valueChanged) {
                  $newLifetime = (int) $setting->value;
                  Config::set('session.lifetime', $newLifetime);
                  
                  try {
                      $path = config_path('session.php');
                      $contents = file_get_contents($path);
                      $contents = preg_replace(
                         "/'lifetime'\s*=>\s*env\('SESSION_LIFETIME',\s*\d+\s*\),/", 
                         "'lifetime' => env('SESSION_LIFETIME', " . $newLifetime . "),", 
                         $contents
                      );
                      if ($contents !== null && $contents !== file_get_contents($path)) {
                          file_put_contents($path, $contents);
                      }
                  } catch (\Exception $e) {
                      \Log::error('Could not update session.php: ' . $e->getMessage());
                  }
             }
         } // Hết vòng lặp
 
         return redirect()->route('settings.edit')->with('success', 'Cấu hình đã được cập nhật.');
     }
}