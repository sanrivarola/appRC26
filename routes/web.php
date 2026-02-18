<?php

use Illuminate\Support\Facades\Route;

/* ───────────── Controllers ───────────── */
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\RelevamientoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AreaInventarioController;
use App\Http\Controllers\SubareaInventarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\PluviometroController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompraLegacyController;
use App\Models\Mantenimiento;
use Carbon\Carbon;

/*──────────────────  HOME & DASHBOARD  ──────────────────*/
Route::get('/', fn () => redirect()->route('login'));


Route::get('/dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*──────────────────  PERFIL  ──────────────────*/
Route::middleware('auth')->group(function () {
    Route::get   ('/profile',  [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch ('/profile',  [ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile',  [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*──────────────────  PANEL ADMIN  ──────────────────*/
/* Dashboard admin */
Route::middleware(['auth','role:admin'])->get('/admin', function () {
    $hoy = Carbon::today();

    $vencidos = Mantenimiento::where('archivado', 0)
        ->whereNotNull('next_due_at')
        ->whereDate('next_due_at', '<', $hoy)
        ->count();

    $porVencer = Mantenimiento::where('archivado', 0)
        ->whereNotNull('next_due_at')
        ->whereDate('next_due_at', '>=', $hoy)
        ->whereDate('next_due_at', '<=', $hoy->copy()->addDays(7))
        ->count();

    return view('dashboard', compact('vencidos', 'porVencer'));
})->name('dashboard');

/* Usuarios */
Route::middleware(['auth','role:admin'])
      ->prefix('usuarios')
      ->name('usuarios.')
      ->controller(App\Http\Controllers\UserController::class)
      ->group(function () {
          Route::get('/',            'index')->name('index');
          Route::get('{user}/edit',  'edit' )->name('edit');
          Route::put('{user}',       'update')->name('update');
      });

/* Horarios */
Route::middleware(['auth','role:admin'])
      ->prefix('horarios')
      ->name('horarios.')
      ->controller(App\Http\Controllers\HorarioAdminController::class)
      ->group(function () {
          Route::get('/',           'index')->name('index');
          Route::post('/export',   'export')->name('export');
          Route::get('{horario}', 'show')->name('show');
      });


/*──────────────────  HORARIO (fichada)  ──────────────────*/
Route::middleware('auth')->controller(HorarioController::class)->group(function () {
    Route::get ('/horario',        'card'   )->name('horario.card');
    Route::post('/horario/entrada','entrada')->name('horario.entrada');
    Route::post('/horario/salida', 'salida' )->name('horario.salida');
});

/*──────────────────  INFORMES (admin|editor) ─────────────*/
Route::middleware(['auth','role:admin|editor'])
      ->get('/informes', fn () => view('informes.index'))
      ->name('informes.index');

/*──────────────────          TAREAS          ─────────────*/
Route::middleware(['auth'])
      ->prefix('tareas')
      ->name('tareas.')
      ->group(function () {

    /* CRUD */
    Route::get ('/',                 [TareaController::class,'index' ])->name('index');
    Route::get ('/create',           [TareaController::class,'create'])->name('create')->middleware('role:admin|editor');
    Route::post('/',                 [TareaController::class,'store' ])->name('store' )->middleware('role:admin|editor');

    Route::get ('/pausadas',         [TareaController::class,'pausadas' ])->name('pausadas' )->middleware('role:admin|creador');
    Route::get ('/archivadas',       [TareaController::class,'archivadas'])->name('archivadas')->middleware('role:admin|creador');

    Route::get ('/{tarea}',          [TareaController::class,'show'  ])->name('show');
    Route::get ('/{tarea}/editar',   [TareaController::class,'edit'  ])->name('edit');
    Route::put ('/{tarea}',          [TareaController::class,'update'])->name('update');
    Route::delete('/{tarea}',        [TareaController::class,'destroy'])->name('destroy');

    /* devoluciones */
    Route::get ('/{tarea}/devoluciones',          [TareaController::class,'devoluciones'])     ->name('devoluciones');
    Route::post('/{tarea}/devoluciones',          [TareaController::class,'guardarDevolucion'])->name('devoluciones.guardar')->middleware('role:colaborador');
    Route::delete('/{tarea}/devoluciones/{devolucion}', [TareaController::class,'destroyDevolucion'])->name('devoluciones.destroy')->middleware('role:colaborador');

    /* extras */
    Route::get ('/{tarea}/extras/create', [TareaController::class,'createExtra'])->name('extras.create');
    Route::post('/{tarea}/extras',        [TareaController::class,'storeExtra' ])->name('extras.store');

    /* export CSV */
    Route::post('/export',[TareaController::class,'export'])
         ->name('export')->middleware('role:admin');
});

/*──────────────────  MANTENIMIENTOS  ───────────────────*/
Route::middleware('auth')->group(function () {

    /* archivados */
    Route::get ('mantenimientos/archivados',           [MantenimientoController::class,'archivados'   ])->name('mantenimientos.archivados');

    /* archivar */
    Route::get ('mantenimientos/{m}/archive',          [MantenimientoController::class,'archiveForm'  ])->name('mantenimientos.archive.form');
    Route::post('mantenimientos/{m}/archive',          [MantenimientoController::class,'archive'      ])->name('mantenimientos.archive');

    /* devoluciones */
    Route::get ('mantenimientos/{m}/devoluciones',     [MantenimientoController::class,'indexDevoluciones'])->name('mantenimientos.devoluciones.index');
    Route::post('mantenimientos/{m}/devoluciones',     [MantenimientoController::class,'storeDevolucion' ])->name('mantenimientos.devoluciones.store');

    /* CRUD estándar */
    Route::resource('mantenimientos', MantenimientoController::class);
});

/*──────────────────  RELEVAMIENTOS  ────────────────────*/
Route::middleware(['auth'])
      ->prefix('relevamientos')
      ->name('relevamientos.')
      ->controller(RelevamientoController::class)
      ->group(function () {

          Route::get ('/',               'index')->name('index');
          Route::get ('/create',         'create')->name('create');
          Route::post('/',               'store' )->name('store');
          Route::get ('/{relevamiento}','show'  )->name('show');
          Route::get ('/{relevamiento}/edit','edit')->name('edit');
          Route::put ('/{relevamiento}', 'update')->name('update');
          Route::delete('/{relevamiento}','destroy')->name('destroy');

          Route::get ('/{relevamiento}/export','export')->name('export')->middleware('role:admin');
      });

/*──────────────────  PROVEEDORES  (admin) ──────────────*/
Route::middleware(['auth','role:admin'])
      ->prefix('proveedores')
      ->name('proveedores.')
      ->controller(ProveedorController::class)
      ->group(function () {

          Route::get ('/',            'index' )->name('index');
          Route::get ('/create',      'create')->name('create');
          Route::post('/',            'store' )->name('store');

          Route::get ('/{proveedor}',       'show'   )->name('show');
          Route::get ('/{proveedor}/edit',  'edit'   )->name('edit');
          Route::put ('/{proveedor}',       'update' )->name('update');
          Route::delete('/{proveedor}',     'destroy')->name('destroy');

          Route::get ('/export', 'export')->name('export');
      });

/*──────────────────  INVENTARIO ────────────────────────*/
Route::middleware(['auth'])->group(function () {

    /* visualización (todos) */
    Route::get('/inventario', [InventarioController::class,'index'])->name('inventario.index');

    /* CRUD (crear/guardar/editar/actualizar/eliminar) */
    Route::get ('/inventario/create',              [InventarioController::class,'create'])->name('inventario.create');
    Route::post('/inventario',                     [InventarioController::class,'store'])->name('inventario.store');
    Route::get ('/inventario/{inventario}/edit',   [InventarioController::class,'edit'])->name('inventario.edit')->whereNumber('inventario');
    Route::put ('/inventario/{inventario}',        [InventarioController::class,'update'])->name('inventario.update')->whereNumber('inventario');
    Route::delete('/inventario/{inventario}',      [InventarioController::class,'destroy'])->name('inventario.destroy')->whereNumber('inventario');

    /* detalle, historial y ajustes (después de CRUD y con restricción numérica) */
    Route::get ('/inventario/{inventario}',        [InventarioController::class,'show'])->name('inventario.show')->whereNumber('inventario');
    Route::post('/inventario/{inventario}/adjust', [InventarioController::class,'adjust'])->name('inventario.adjust')->whereNumber('inventario');
    Route::get ('/inventario/{inventario}/historial', [InventarioController::class,'history'])->name('inventario.history')->whereNumber('inventario');

    /* compra rápida desde inventario */
    Route::post('/inventario/{inventario}/comprar', [CompraController::class,'crearDesdeInventario'])->name('inventario.comprar')->whereNumber('inventario');
    /* exportar */
    Route::get('/inventario/export/csv', [InventarioController::class, 'exportCsv'])
    ->name('inventario.export');
    Route::get('/inventario/export/xlsx', [InventarioController::class, 'exportXlsx'])
    ->middleware('role:admin')
    ->name('inventario.export.xlsx');
});



/* CRUD de inventario sólo admin -----------------------------------------*/
Route::middleware(['auth','role:admin'])->group(function () {

    /* áreas */
    Route::resource('areas', AreaInventarioController::class)->except(['show'])->names('areas');

    /* subáreas */
    Route::resource('subareas', SubareaInventarioController::class)->except(['show'])->names('subareas');

    /* inventario CRUD */
    Route::resource('inventario', InventarioController::class)
         ->except(['index','show']);
});

/*──────────────────  COMPRAS  ─────────────────────────*/
Route::middleware('auth')->group(function () {

        /* ✅ listado de realizadas — debe ir ANTES del resource para evitar colisión */
        Route::get('compras/realizadas', [CompraController::class,'realizadas'])
             ->name('compras.realizadas');
    
        /* resource estándar */
        Route::resource('compras', CompraController::class);
    
        Route::middleware('auth')->group(function () {
    
        // Historial de compras realizadas
        Route::get('/compras/realizadas', [CompraController::class, 'realizadas'])
             ->name('compras.realizadas');
    
        // CRUD /compras (resource) – deja esto tal como lo tenías
        Route::resource('compras', CompraController::class);
    });
});


Route::get('/compras-legacy', [CompraLegacyController::class, 'index'])
  ->name('compras.legacy.index')->middleware('auth');

Route::get('/compras-legacy/{compra}', [CompraLegacyController::class, 'show'])
  ->name('compras.legacy.show')->middleware('auth');

Route::put('/compras/{id}/archivar', [CompraController::class, 'archivar'])
    ->name('compras.archivar')
    ->middleware('auth');

/*──────────────────  PLUVIÓMETRO  ─────────────────────*/
Route::middleware('auth')
      ->resource('pluviometro', PluviometroController::class)
      ->names('pluviometro')
      ->except(['show']);   // de momento sin “show”

require __DIR__.'/auth.php';
