<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\User;
use App\Models\UserActivity;
use Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Get outlet information
        $outlet = Outlet::first();
        
        // Get settings by group
        $generalSettings = Setting::getGroup('general');
        $receiptSettings = Setting::getGroup('receipt');
        $paymentSettings = Setting::getGroup('payment');
        $systemSettings = Setting::getGroup('system');
        
        // Get categories for management
        $categories = Category::orderBy('name')->get();
        
        // Get system statistics
        $stats = [
            'total_users' => User::count(),
            'total_categories' => Category::count(),
            'total_products' => \App\Models\Product::count(),
            'total_sales' => \App\Models\Sale::count(),
            'database_size' => $this->getDatabaseSize(),
            'storage_size' => $this->getStorageSize(),
        ];
        
        return view('admin.settings.index', compact(
            'outlet',
            'generalSettings',
            'receiptSettings', 
            'paymentSettings',
            'systemSettings',
            'categories',
            'stats'
        ));
    }
    
    public function updateOutlet(Request $request)
    {
        $outlet = Outlet::first();
        
        if (!$outlet) {
            return redirect()->back()->with('error', 'No outlet found.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
        ]);
        
        $outlet->update($validated);
        
        // Log activity
        UserActivity::log(
            'outlet_update',
            'Updated outlet information: ' . $outlet->name,
            ['outlet_id' => $outlet->id]
        );
        
        return redirect()->back()->with('success', 'Outlet information updated successfully.');
    }
    
    public function updateSettings(Request $request)
    {
        $settings = $request->input('settings', []);
        
        foreach ($settings as $key => $value) {
            // Determine the type and group based on key
            [$group, $type] = $this->determineSettingGroupAndType($key);
            
            Setting::set($key, $value, $type, $group);
        }
        
        // Log activity
        UserActivity::log(
            'settings_update',
            'Updated system settings',
            ['settings_changed' => array_keys($settings)]
        );
        
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    
    public function createCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);
        
        $category = Category::create($validated);
        
        // Log activity
        UserActivity::log(
            'category_create',
            'Created new category: ' . $category->name,
            ['category_id' => $category->id]
        );
        
        return redirect()->back()->with('success', 'Category created successfully.');
    }
    
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);
        
        $category->update($validated);
        
        // Log activity
        UserActivity::log(
            'category_update',
            'Updated category: ' . $category->name,
            ['category_id' => $category->id]
        );
        
        return redirect()->back()->with('success', 'Category updated successfully.');
    }
    
    public function deleteCategory(Category $category)
    {
        $categoryName = $category->name;
        
        // Check if category has products
        $productCount = $category->products()->count();
        if ($productCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with products. Move or delete products first.');
        }
        
        $category->delete();
        
        // Log activity
        UserActivity::log(
            'category_delete',
            'Deleted category: ' . $categoryName,
            ['category_name' => $categoryName]
        );
        
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
    
    public function backup()
    {
        try {
            // Create backup timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupName = 'kasir_lite_backup_' . $timestamp . '.sql';
            
            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $fullPath = $backupPath . '/' . $backupName;
            
            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );
            
            // Execute backup
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($fullPath)) {
                // Log activity
                UserActivity::log(
                    'backup_create',
                    'Created database backup',
                    ['backup_file' => $backupName, 'size' => filesize($fullPath)]
                );
                
                return response()->download($fullPath)->deleteFileAfterSend();
            } else {
                return redirect()->back()->with('error', 'Failed to create backup. Please check your database configuration.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    public function clearCache()
    {
        try {
            // Clear various caches
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            
            // Log activity
            UserActivity::log(
                'cache_clear',
                'Cleared system cache',
                ['caches' => ['cache', 'config', 'route', 'view']]
            );
            
            return redirect()->back()->with('success', 'System cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    private function determineSettingGroupAndType($key)
    {
        // Default values
        $group = 'general';
        $type = 'string';
        
        // Determine group and type based on key prefix/name
        if (strpos($key, 'receipt_') === 0) {
            $group = 'receipt';
        } elseif (strpos($key, 'payment_') === 0) {
            $group = 'payment';
        } elseif (strpos($key, 'tax_') === 0) {
            $group = 'general';
            $type = 'number';
        } elseif (in_array($key, ['auto_backup', 'email_notifications', 'sms_notifications'])) {
            $type = 'boolean';
        } elseif (in_array($key, ['backup_retention_days', 'session_timeout'])) {
            $type = 'number';
            $group = 'system';
        }
        
        return [$group, $type];
    }
    
    private function getDatabaseSize()
    {
        try {
            $database = config('database.connections.mysql.database');
            $result = \DB::select(
                "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'DB_Size_MB' 
                 FROM information_schema.tables 
                 WHERE table_schema = ?",
                [$database]
            );
            
            return ($result[0]->DB_Size_MB ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getStorageSize()
    {
        try {
            $bytes = 0;
            $path = storage_path('app');
            
            if (is_dir($path)) {
                foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                    if ($file->isFile()) {
                        $bytes += $file->getSize();
                    }
                }
            }
            
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
