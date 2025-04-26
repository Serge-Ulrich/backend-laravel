<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MessageController;

// 🧑 Authentification (publique)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 🔐 Routes protégées (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Infos utilisateur connecté
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/update', [AuthController::class, 'update']);
    Route::post('/messages', [MessageController::class, 'store']); // Envoyer un message
Route::get('/messages/received/{receiver_id}', [MessageController::class, 'received']); // Messages reçus
Route::get('/messages/sent/{sender_id}', [MessageController::class, 'sent']); // Messages envoyés

    // Gestion des commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 📦 Produits (publics)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
