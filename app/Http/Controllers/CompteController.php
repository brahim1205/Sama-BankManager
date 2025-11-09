<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compte;
use App\Http\Resources\CompteRessource;
use App\Http\Resources\MetaRessource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;


class CompteController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $cacheKey = 'comptes_' . md5(json_encode($request->all()));
            $cacheData = Cache::get($cacheKey);

            // if ($cacheData) {
            //     return $this->successResponse($cacheData);
            // }

            $comptes = Compte::filtrerComptes($request->all(), $user)
                ->paginate(min($request->get('limit', 10), 100))
                ->appends($request->all());

            $data = [
                'data' => CompteRessource::collection($comptes),
                'meta' => new MetaRessource($comptes)
            ];

            Cache::put($cacheKey, CompteRessource::collection($comptes), now()->addMinutes(10));

            return $this->successResponse($data, 'comptes recuperer avec succes ! ', $comptes->total(), $user->id, $user->isAdmin());

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des comptes: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $compte = Compte::where('id', $id)->first();

            if (!$compte) {
                return $this->errorResponse('Compte non trouvé', 404);
            }

            // Vérifier si l'utilisateur peut voir ce compte
            if (!$user->isAdmin() && $compte->user_id !== $user->id) {
                return $this->errorResponse('Accès non autorisé à ce compte', 403);
            }

            return $this->successResponse(new CompteRessource($compte), 'Compte récupéré avec succès');

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération du compte: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $compte = Compte::where('id', $id)->first();

            if (!$compte) {
                return $this->errorResponse('Compte non trouvé', 404);
            }

            // Vérifier si l'utilisateur peut supprimer ce compte
            if (!$user->isAdmin() && $compte->user_id !== $user->id) {
                return $this->errorResponse('Accès non autorisé à ce compte', 403);
            }

            // Soft delete
            $compte->delete();

            // Invalider le cache
            Cache::flush();

            return $this->successResponse(null, 'Compte supprimé avec succès');

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la suppression du compte: ' . $e->getMessage(), 500);
        }
    }

    public function archive($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $compte = Compte::where('id', $id)->first();

            if (!$compte) {
                return $this->errorResponse('Compte non trouvé', 404);
            }

            // Vérifier si l'utilisateur peut archiver ce compte
            if (!$user->isAdmin() && $compte->user_id !== $user->id) {
                return $this->errorResponse('Accès non autorisé à ce compte', 403);
            }

            $compte->update(['is_archived' => true]);

            // Invalider le cache
            Cache::flush();

            return $this->successResponse(new CompteRessource($compte), 'Compte archivé avec succès');

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'archivage du compte: ' . $e->getMessage(), 500);
        }
    }

    public function unarchive($id)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $compte = Compte::where('id', $id)->first();

            if (!$compte) {
                return $this->errorResponse('Compte non trouvé', 404);
            }

            // Vérifier si l'utilisateur peut désarchiver ce compte
            if (!$user->isAdmin() && $compte->user_id !== $user->id) {
                return $this->errorResponse('Accès non autorisé à ce compte', 403);
            }

            $compte->update(['is_archived' => false]);

            // Invalider le cache
            Cache::flush();

            return $this->successResponse(new CompteRessource($compte), 'Compte désarchivé avec succès');

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du désarchivage du compte: ' . $e->getMessage(), 500);
        }
    }

    public function archived(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $comptes = Compte::archived();

            // Filtrer par utilisateur si pas admin
            if (!$user->isAdmin()) {
                $comptes->where('user_id', $user->id);
            }

            $comptes = $comptes->paginate(min($request->get('limit', 10), 100))
                ->appends($request->all());

            $data = [
                'data' => CompteRessource::collection($comptes),
                'meta' => new MetaRessource($comptes)
            ];

            return $this->successResponse($data, 'Comptes archivés récupérés avec succès', $comptes->total(), $user->id, $user->isAdmin());

        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des comptes archivés: ' . $e->getMessage(), 500);
        }
    }
}
