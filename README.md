## Panduan Setup dan Pengembangan Multi-Tenant

### Initial Setup

1. **Konfigurasi Database**
   
   Pastikan tabel-tabel tenant memiliki kolom `vendor_id`:
   ```php
   Schema::create('pelanggans', function (Blueprint $table) {
       $table->id();
       $table->foreignId('vendor_id')->constrained('vendors');
       // kolom lainnya
       $table->timestamps();
       
       // Tambahkan index untuk performa query
       $table->index('vendor_id');
   });
   ```

2. **Registrasi Service Provider**
   
   Di `bootstrap/providers.php`:
   ```php
   return [
       App\Providers\AppServiceProvider::class,
       App\Providers\TenantServiceProvider::class,
       // provider lainnya
   ];
   ```

3. **Registrasi Middleware**
   
   Di `bootstrap/app.php`:
   ```php
   $middleware->alias([
       'dev' => \App\Http\Middleware\DevMiddleware::class,
       'vendor' => \App\Http\Middleware\VendorMiddleware::class,
       'tenants' => \App\Http\Middleware\SetTenantContext::class,
   ]);
   ```

4. **Registrasi Facade**
   
   Di `config/app.php` (untuk Laravel < 11) atau dengan metode `withFacades` di `bootstrap/app.php` (untuk Laravel 11):
   ```php
   'aliases' => [
       // Alias lainnya
       'Tenant' => App\Facades\Tenant::class,
   ],
   ```

### Membuat Model Tenant Baru

1. **Buat Model yang Extend TenantModel**
   ```bash
   php artisan make:model Vendor/NamaModel -m
   ```

2. **Edit Model**
   ```php
   namespace App\Models\Vendor;
   
   class NamaModel extends TenantModel
   {
       protected $fillable = [
           'field1', 'field2'
           // Tidak perlu menambahkan vendor_id ke fillable
       ];
       
       // Relasi dan method lainnya
   }
   ```

3. **Edit Migration**
   ```php
   Schema::create('nama_models', function (Blueprint $table) {
       $table->id();
       $table->foreignId('vendor_id')->constrained('vendors');
       $table->string('field1');
       $table->text('field2');
       $table->timestamps();
       
       $table->index('vendor_id');
   });
   ```

### Mengembangkan Fitur Baru dengan Multi-Tenant

1. **Controller dengan Tenant Awareness**
   ```bash
   php artisan make:controller Vendor/NamaModelController --resource
   ```

   ```php
   namespace App\Http\Controllers\Vendor;
   
   use App\Http\Controllers\Controller;
   use App\Models\Vendor\NamaModel;
   use App\Facades\Tenant;
   
   class NamaModelController extends Controller
   {
       public function index()
       {
           // Data sudah otomatis difilter berdasarkan tenant
           $items = NamaModel::all();
           return view('nama_model.index', compact('items'));
       }
       
       public function store(Request $request)
       {
           // vendor_id otomatis ditambahkan oleh TenantModel
           $item = NamaModel::create($request->validated());
           return redirect()->route('nama_model.index');
       }
       
       // Method lainnya
   }
   ```

2. **Routes dengan Tenant Middleware**
   ```php
   Route::middleware(['auth', 'verified', 'tenants', 'vendor'])->group(function () {
       Route::resource('nama_model', NamaModelController::class);
   });
   ```

3. **Blade Views dengan Tenant Context**
   ```php
   <div class="card">
       <div class="card-header">
           <h3>{{ Tenant::getVendor()->name }} - Daftar Item</h3>
       </div>
       <div class="card-body">
           <!-- Tampilkan data -->
       </div>
   </div>
   ```

### Pengembangan Advanced

1. **Tenant-Specific Configuration**
   
   Membuat konfigurasi spesifik per tenant:
   ```php
   // Di TenantManager.php
   public function getConfig($key, $default = null)
   {
       $vendor = $this->getVendor();
       if (!$vendor) return $default;
       
       // Ambil dari database atau cache
       $configs = Cache::remember("vendor_config_{$vendor->id}", 3600, function() use ($vendor) {
           return VendorConfig::where('vendor_id', $vendor->id)->pluck('value', 'key')->toArray();
       });
       
       return $configs[$key] ?? $default;
   }
   ```

2. **Tenant-Specific Filesystem**
   
   Mengatur disk storage per tenant:
   ```php
   // Di AppServiceProvider.php
   public function boot()
   {
       Storage::extend('tenant', function ($app, $config) {
           $vendorId = Tenant::getVendorId();
           $driver = $config['driver'];
           
           $config['root'] = $config['root'] . '/' . $vendorId;
           
           return Storage::createDriverInstance($driver, $config);
       });
   }
   ```

3. **Tenant-Specific Validation Rules**
   
   Membuat aturan validasi spesifik per tenant:
   ```php
   Validator::extend('unique_in_tenant', function ($attribute, $value, $parameters, $validator) {
       $table = $parameters[0] ?? null;
       $column = $parameters[1] ?? $attribute;
       $ignore = $parameters[2] ?? null;
       
       $query = DB::table($table)
           ->where($column, $value)
           ->where('vendor_id', Tenant::getVendorId());
           
       if ($ignore) {
           $query->where('id', '!=', $ignore);
       }
       
       return $query->count() === 0;
   });
   ```

4. **Tenant-Specific Events**
   
   Membuat event dan listener spesifik per tenant:
   ```php
   // Event
   class TenantSpecificEvent
   {
       public $vendorId;
       public $data;
       
       public function __construct($data)
       {
           $this->vendorId = Tenant::getVendorId();
           $this->data = $data;
       }
   }
   
   // Listener
   class TenantSpecificListener
   {
       public function handle(TenantSpecificEvent $event)
       {
           // Pastikan listener berjalan dalam konteks tenant yang benar
           Tenant::forVendor($event->vendorId, function() use ($event) {
               // Proses event
           });
       }
   }
   ```

### Testing Multi-Tenant

1. **Unit Testing dengan Tenant Context**
   ```php
   public function test_can_create_customer()
   {
       // Buat vendor untuk testing
       $vendor = Vendor::factory()->create();
       
       // Set tenant context
       Tenant::setVendorId($vendor->id);
       
       // Lakukan test
       $response = $this->post('/dashboard/pelanggan', [
           'nama' => 'Test Customer',
           // data lainnya
       ]);
       
       $response->assertRedirect();
       $this->assertDatabaseHas('pelanggans', [
           'nama' => 'Test Customer',
           'vendor_id' => $vendor->id
       ]);
   }
   ```

2. **Feature Testing Multi-Tenant**
   ```php
   public function test_tenant_isolation()
   {
       // Buat dua vendor
       $vendor1 = Vendor::factory()->create();
       $vendor2 = Vendor::factory()->create();
       
       // Buat user untuk masing-masing vendor
       $user1 = User::factory()->create(['usertype' => 'vendor']);
       $user2 = User::factory()->create(['usertype' => 'vendor']);
       
       $user1->vendorUser()->attach($vendor1->id);
       $user2->vendorUser()->attach($vendor2->id);
       
       // Buat data untuk vendor1
       Tenant::forVendor($vendor1->id, function() {
           Pelanggan::create(['nama' => 'Customer Vendor 1']);
       });
       
       // Buat data untuk vendor2
       Tenant::forVendor($vendor2->id, function() {
           Pelanggan::create(['nama' => 'Customer Vendor 2']);
       });
       
       // Test user1 hanya bisa melihat data vendor1
       $this->actingAs($user1)
           ->get('/dashboard/pelanggan')
           ->assertSee('Customer Vendor 1')
           ->assertDontSee('Customer Vendor 2');
       
       // Test user2 hanya bisa melihat data vendor2
       $this->actingAs($user2)
           ->get('/dashboard/pelanggan')
           ->assertSee('Customer Vendor 2')
           ->assertDontSee('Customer Vendor 1');
   }
   ```

### Troubleshooting Multi-Tenant

1. **Debugging Tenant Context**
   ```php
   // Tambahkan di AppServiceProvider::boot
   if (config('app.debug')) {
       DB::listen(function($query) {
           $vendorId = Tenant::getVendorId();
           Log::debug("SQL [{$vendorId}]: " . $query->sql, [
               'bindings' => $query->bindings,
               'time' => $query->time
           ]);
       });
   }
   ```

2. **Tenant Context Missing**
   
   Jika mengalami error "vendor_id cannot be null", periksa:
   - Middleware `tenants` sudah terdaftar di route
   - User memiliki relasi dengan vendor
   - `SetTenantContext` middleware berjalan dengan benar
   - Tidak ada operasi database sebelum tenant context diset

3. **Data Leakage Antar Tenant**
   
   Jika data "bocor" antar tenant:
   - Pastikan model mengextend `TenantModel`
   - Periksa query raw atau builder yang mungkin melewati global scope
   - Periksa relasi antar model apakah sudah benar
   - Pastikan tidak ada query yang menggunakan `withoutGlobalScopes()`

## Core Features
- [x] User Authentication & Authorization
- [x] Multi-vendor Management
- [x] Customer Management
- [x] Product Management
- [x] Point of Sale (POS)
- [x] Production Estimation
- [x] Materials & Equipment Management
- [x] Sales Reporting
- [] Email Notifications
- [] Stock Notification
- [] Switch Multi Vendor Dashboard
- [] User role dan permission
- [] profile user
- [] profile user vendor


## Dashboard Features
### Admin Dashboard (Developer)
- [x] User Statistics
- [x] Vendor Management
- [x] User Management
- [] System Monitoring Laravel Pulse

### Vendor Dashboard
- [x] Sales Analytics
- [x] Product Performance
- [x] Monthly Revenue Charts
- [x] Daily Transaction Overview
- [x] Customer Management
- [x] Inventory Control

## Color Palette
### Primary Colors
- Primary Blue: `#2196F3`

### Button Styles
- Primary: `#2196F3`
- Secondary: `#757575`
- Success: `#4CAF50`
- Danger: `#F44336`
- Warning: `#FFC107`
- Info: `#03A9F4`

### Background Colors
- Main: `#FFFFFF`
- Light: `#F5F5F5`
- Dark: `#111111`

## Troubleshooting
### Multi-Tenant Issues
1. Ensure models extend `TenantModel`
2. Verify tenant middleware is properly configured
3. Check model relationships for correct tenant scoping
4. Debug tenant context using `Tenant::getVendorId()`
5. Review bulk operations for proper tenant scoping

## Contributing
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add some amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Create a Pull Request

## License
Copyright © 2023 Grafika Printing. All rights reserved.