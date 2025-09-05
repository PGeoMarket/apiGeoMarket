<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Seller;
use App\Models\Phone;
use App\Models\Category;
use App\Models\Publication;
use App\Models\Comment;
use App\Models\ReasonComplaint;
use App\Models\Complaint;
use App\Models\Coordinate;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ORMController extends Controller
{
    /**
     * Mostrar Role con sus relaciones
     */
    public function showRole(Role $role): JsonResponse
    {
        $role->load(['users']);
        
        return response()->json([
            'role' => $role
        ]);
    }

    /**
     * Mostrar User con sus relaciones
     */
    public function showUser(User $user): JsonResponse
    {
        $user->load([
            'role',
            'seller',
            'comments',
            'complaints',
            'favoritePublications', // tabla pivot publication_user
        ]);
        
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Mostrar Seller con sus relaciones
     */
    public function showSeller(Seller $seller): JsonResponse
    {
        $seller->load([
            'user',
            'phones',
            'publications',
            'coordinate', // relación polimórfica
            'image'       // relación polimórfica
        ]);
        
        return response()->json([
            'seller' => $seller
        ]);
    }

    /**
     * Mostrar Phone con sus relaciones
     */
    public function showPhone(Phone $phone): JsonResponse
    {
        $phone->load(['seller']);
        
        return response()->json([
            'phone' => $phone
        ]);
    }

    /**
     * Mostrar Category con sus relaciones
     */
    public function showCategory(Category $category): JsonResponse
    {
        $category->load(['publications']);
        
        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * Mostrar Publication con sus relaciones
     */
    public function showPublication(Publication $publication): JsonResponse
    {
        $publication->load([
            'seller',
            'category',
            'comments',
            'complaints',
            'usersWhoFavorited', // tabla pivot publication_user
            'image'            // relación polimórfica
        ]);
        
        return response()->json([
            'publication' => $publication
        ]);
    }

    /**
     * Mostrar Comment con sus relaciones
     */
    public function showComment(Comment $comment): JsonResponse
    {
        $comment->load([
            'user',
            'publication'
        ]);
        
        return response()->json([
            'comment' => $comment
        ]);
    }

    /**
     * Mostrar ReasonComplaint con sus relaciones
     */
    public function showReasonComplaint(ReasonComplaint $reasonComplaint): JsonResponse
    {
        $reasonComplaint->load(['complaints']);
        
        return response()->json([
            'reasonComplaint' => $reasonComplaint
        ]);
    }

    /**
     * Mostrar Complaint con sus relaciones
     */
    public function showComplaint(Complaint $complaint): JsonResponse
    {
        $complaint->load([
            'user',
            'publication',
            'reasonComplaint' // Nota: en la migración es 'reason_id' que apunta a 'reason_complaints'
        ]);
        
        return response()->json([
            'complaint' => $complaint
        ]);
    }

    /**
     * Mostrar Coordinate con sus relaciones polimórficas
     */
    public function showCoordinate(Coordinate $coordinate): JsonResponse
    {
        $coordinate->load(['coordinateable']); // relación polimórfica
        
        return response()->json([
            'coordinate' => $coordinate
        ]);
    }

    /**
     * Mostrar Image con sus relaciones polimórficas
     */
    public function showImage(Image $image): JsonResponse
    {
        $image->load(['imageable']); // relación polimórfica
        
        return response()->json([
            'image' => $image
        ]);
    }

    /**
     * Método principal que prueba todas las relaciones con UNA SOLA RUTA
     */
    public function testAllRelations(Request $request): JsonResponse
    {
        $results = [];

        try {
            // Role - primer registro
            $role = Role::first();
            if ($role) {
                $role->load(['users']);
                $results['role'] = $role;
            }

            // User - primer registro
            $user = User::first();
            if ($user) {
                $user->load([
                    'role',
                    'seller',
                    'comments',
                    'complaints',
                    'favoritePublications',
                ]);
                $results['user'] = $user;
            }

            // Seller - primer registro
            $seller = Seller::first();
            if ($seller) {
                $seller->load([
                    'user',
                    'phones',
                    'publications',
                    'coordinate',
                    'image'
                ]);
                $results['seller'] = $seller;
            }

            // Phone - primer registro
            $phone = Phone::first();
            if ($phone) {
                $phone->load(['seller']);
                $results['phone'] = $phone;
            }

            // Category - primer registro
            $category = Category::first();
            if ($category) {
                $category->load(['publications']);
                $results['category'] = $category;
            }

            // Publication - primer registro
            $publication = Publication::first();
            if ($publication) {
                $publication->load([
                    'seller',
                    'category',
                    'comments',
                    'complaints',
                    'usersWhoFavorited',
                    'image'
                ]);
                $results['publication'] = $publication;
            }

            // Comment - primer registro
            $comment = Comment::first();
            if ($comment) {
                $comment->load([
                    'user',
                    'publication'
                ]);
                $results['comment'] = $comment;
            }

            // ReasonComplaint - primer registro
            $reasonComplaint = ReasonComplaint::first();
            if ($reasonComplaint) {
                $reasonComplaint->load(['complaints']);
                $results['reasonComplaint'] = $reasonComplaint;
            }

            // Complaint - primer registro
            $complaint = Complaint::first();
            if ($complaint) {
                $complaint->load([
                    'user',
                    'publication',
                    'reasonComplaint'
                ]);
                $results['complaint'] = $complaint;
            }

            // Coordinate - primer registro (relación polimórfica)
            $coordinate = Coordinate::first();
            if ($coordinate) {
                $coordinate->load(['coordinateable']);
                $results['coordinate'] = $coordinate;
            }

            // Image - primer registro (relación polimórfica)
            $image = Image::first();
            if ($image) {
                $image->load(['imageable']);
                $results['image'] = $image;
            }

            return response()->json([
                'success' => true,
                'message' => 'Todas las relaciones probadas exitosamente',
                'total_models_tested' => count($results),
                'models_found' => array_keys($results),
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar las relaciones',
                'error' => $e->getMessage(),
                'data' => $results
            ], 500);
        }
    }

    /**
     * Método para probar específicamente las relaciones polimórficas
     */
    public function testPolymorphicRelations(): JsonResponse
    {
        $results = [];

        try {
            // Coordenadas de sellers
            $sellerCoordinate = Coordinate::where('coordinateable_type', 'App\\Models\\Seller')->with('coordinateable')->get();
            $results['seller_coordinate'] = $sellerCoordinate;

            // Coordenadas de publicaciones
            $userCoordinate = Coordinate::where('coordinateable_type', 'App\\Models\\User')->with('coordinateable')->get();
            $results['user_coordinate'] = $userCoordinate;

            // Imágenes de sellers
            $sellerImage = Image::where('imageable_type', 'App\\Models\\Seller')->with('imageable')->get();
            $results['seller_image'] = $sellerImage;

            // Imágenes de publicaciones
            $publicationImage = Image::where('imageable_type', 'App\\Models\\Publication')->with('imageable')->get();
            $results['publication_image'] = $publicationImage;

            return response()->json([
                'success' => true,
                'message' => 'Relaciones polimórficas probadas exitosamente',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar las relaciones polimórficas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

// =====================================================================
// RUTAS PARA AGREGAR EN routes/api.php
// =====================================================================

/*
// OPCIÓN 1: Si quieres ambas rutas
Route::prefix('orm-test')->group(function () {
    // Rutas individuales
    Route::get('/roles/{role}', [ORMController::class, 'showRole']);
    Route::get('/users/{user}', [ORMController::class, 'showUser']);
    Route::get('/sellers/{seller}', [ORMController::class, 'showSeller']);
    Route::get('/phones/{phone}', [ORMController::class, 'showPhone']);
    Route::get('/categories/{category}', [ORMController::class, 'showCategory']);
    Route::get('/publications/{publication}', [ORMController::class, 'showPublication']);
    Route::get('/comments/{comment}', [ORMController::class, 'showComment']);
    Route::get('/reason-complaints/{reasonComplaint}', [ORMController::class, 'showReasonComplaint']);
    Route::get('/complaints/{complaint}', [ORMController::class, 'showComplaint']);
    Route::get('/coordinate/{coordinate}', [ORMController::class, 'showCoordinate']);
    Route::get('/image/{image}', [ORMController::class, 'showImage']);
    
    // Rutas de prueba masiva
    Route::get('/all-relations', [ORMController::class, 'testAllRelations']);
    Route::get('/polymorphic-relations', [ORMController::class, 'testPolymorphicRelations']); // OPCIONAL
});

// OPCIÓN 2: Si prefieres solo UNA ruta de prueba (MÁS SIMPLE)
Route::prefix('orm-test')->group(function () {
    // Rutas individuales
    Route::get('/roles/{role}', [ORMController::class, 'showRole']);
    Route::get('/users/{user}', [ORMController::class, 'showUser']);
    Route::get('/sellers/{seller}', [ORMController::class, 'showSeller']);
    Route::get('/phones/{phone}', [ORMController::class, 'showPhone']);
    Route::get('/categories/{category}', [ORMController::class, 'showCategory']);
    Route::get('/publications/{publication}', [ORMController::class, 'showPublication']);
    Route::get('/comments/{comment}', [ORMController::class, 'showComment']);
    Route::get('/reason-complaints/{reasonComplaint}', [ORMController::class, 'showReasonComplaint']);
    Route::get('/complaints/{complaint}', [ORMController::class, 'showComplaint']);
    Route::get('/coordinate/{coordinate}', [ORMController::class, 'showCoordinate']);
    Route::get('/image/{image}', [ORMController::class, 'showImage']);
    
    // Solo una ruta de prueba que incluye todo
    Route::get('/test', [ORMController::class, 'testAllRelations']);
});
*/